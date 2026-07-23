<?php

namespace Modules\MobileApp\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Controllers\Controller;
use App\Models\Tenant\DocumentPayment;
use Modules\MobileApp\Models\ReceivedPayment;
use App\Http\Requests\Tenant\DocumentPaymentRequest;
use App\Http\Controllers\Tenant\DocumentPaymentController as DocumentPaymentControllerWeb;
use Modules\MobileApp\Http\Requests\Api\ReceivedPaymentStoreRequest;
use Modules\MobileApp\Http\Requests\Api\ReceivedPaymentClaimRequest;
use Modules\MobileApp\Http\Resources\Api\ReceivedPaymentCollection;

/**
 * Bandeja de pagos recibidos (Yape / Plin).
 *
 * Flujo:
 *  - El equipo-oido empuja cada pago capturado (store), idempotente por dedup_hash.
 *  - Todos los dispositivos del negocio listan la bandeja (byScroll).
 *  - Un usuario reclama un pago pendiente (claim): se asigna de forma atomica a
 *    un comprobante reutilizando DocumentPaymentController@store, que registra el
 *    pago global y lo asocia a la caja. El comprobante pasa a pagado.
 *  - Un pago ya reclamado puede actualizarse (update) o, si seguia pendiente,
 *    descartarse (discard).
 *
 * Esta bandeja no crea relaciones que afecten a la tabla documents: solo guarda
 * de forma desacoplada el id del comprobante y del pago generados.
 */
class ReceivedPaymentController extends Controller
{

    /**
     *
     * Push del equipo-oido: registra un pago capturado.
     *
     * Idempotente por dedup_hash: un re-push del mismo pago no duplica el registro,
     * devuelve el existente con duplicated = true.
     *
     * @param  ReceivedPaymentStoreRequest $request
     * @return array
     */
    public function store(ReceivedPaymentStoreRequest $request)
    {
        $existing = ReceivedPayment::where('dedup_hash', $request->input('dedup_hash'))->first();

        if ($existing) {
            return $this->buildResponse(true, 'El pago ya habia sido registrado', $existing, ['duplicated' => true]);
        }

        try {
            $record = ReceivedPayment::create(array_merge($request->validated(), [
                'status' => ReceivedPayment::STATUS_PENDING,
            ]));
        } catch (QueryException $e) {
            // Carrera entre dos push simultaneos con el mismo dedup_hash:
            // el indice unico protege y devolvemos el registro ya existente.
            $record = ReceivedPayment::where('dedup_hash', $request->input('dedup_hash'))->first();

            if (!$record) {
                throw $e;
            }

            return $this->buildResponse(true, 'El pago ya habia sido registrado', $record, ['duplicated' => true]);
        }

        return $this->buildResponse(true, 'Pago registrado con exito', $record, ['duplicated' => false]);
    }


    /**
     *
     * Listado de la bandeja con scroll infinito (cursor-based pagination).
     *
     * Bandeja compartida: no se filtra por usuario, todos los dispositivos del
     * negocio ven los mismos pagos.
     *
     * Parametros soportados:
     * - limit: cantidad de registros (maximo 100, default tenant.items_per_page)
     * - cursor: posicion actual (null en primera peticion)
     * - status: pending | matched | discarded
     * - provider: YAPE | PLIN
     * - device_id: equipo-oido que capturo el pago
     * - input: busqueda parcial por remitente o texto de la notificacion
     *
     * @param  Request $request
     * @return array
     */
    public function byScroll(Request $request)
    {
        $limit = min((int) $request->input('limit', config('tenant.items_per_page', 15)), 100);
        $cursor = $request->input('cursor');

        $query = ReceivedPayment::whereFilterApi($request)->orderBy('id', 'desc');

        $records = $cursor
            ? $query->cursorPaginate($limit, ['*'], 'cursor', $cursor)
            : $query->cursorPaginate($limit);

        return [
            'success' => true,
            'data' => new ReceivedPaymentCollection($records),
            'pagination' => [
                'next_cursor' => $records->nextCursor()?->encode() ?? null,
                'has_more'    => $records->hasMorePages(),
            ],
        ];
    }


    /**
     *
     * Reclamo atomico de un pago pendiente y asignacion al comprobante.
     *
     * Transicion pending -> matched con candado (lockForUpdate): si otro
     * dispositivo lo reclamo primero responde 409. Internamente ejecuta la logica
     * de DocumentPaymentController@store para registrar el pago en el comprobante
     * (que se emitio pendiente de pago), usando el monto y la fecha del pago
     * capturado y el metodo/destino enviados por el cliente.
     *
     * Body:
     * - document_id: comprobante a pagar
     * - payment_method_type_id: metodo de pago (lo reconoce el front)
     * - payment_destination_id: destino del pago ('cash' o id de cuenta bancaria)
     *
     * @param  int $id
     * @param  ReceivedPaymentClaimRequest $request
     * @return array
     */
    public function claim($id, ReceivedPaymentClaimRequest $request)
    {
        $record = DB::connection('tenant')->transaction(function () use ($id, $request) {

            $received = ReceivedPayment::where('id', $id)->lockForUpdate()->firstOrFail();

            if (!$received->isPending()) {
                $this->throwConflict('El pago ya fue procesado por otro dispositivo.');
            }

            $store = $this->runDocumentPaymentStore($received, $request);

            $received->status = ReceivedPayment::STATUS_MATCHED;
            $received->matched_document_id = (int) $request->input('document_id');
            $received->matched_document_payment_id = $store['id'];
            $received->claimed_by_user_id = auth()->id();
            $received->save();

            return $received;
        });

        return $this->buildResponse(true, 'Pago reclamado y asignado al comprobante con exito', $record);
    }


    /**
     *
     * Actualizar un pago ya reclamado.
     *
     * El store web siempre crea un global_payment y un cash_document_payment
     * nuevos (incluso al editar), por lo que reutilizarlo en modo edicion
     * duplicaria esos registros. Para mantener la consistencia se hace un
     * reemplazo limpio: dentro de la transaccion se elimina el pago anterior con
     * sus relaciones y se genera uno nuevo con los datos enviados.
     *
     * @param  int $id
     * @param  ReceivedPaymentClaimRequest $request
     * @return array
     */
    public function update($id, ReceivedPaymentClaimRequest $request)
    {
        $record = DB::connection('tenant')->transaction(function () use ($id, $request) {

            $received = ReceivedPayment::where('id', $id)->lockForUpdate()->firstOrFail();

            if (!$received->isMatched()) {
                $this->throwConflict('Solo se puede actualizar un pago que ya fue reclamado.', 422);
            }

            $this->removeMatchedDocumentPayment($received);

            $store = $this->runDocumentPaymentStore($received, $request);

            $received->matched_document_id = (int) $request->input('document_id');
            $received->matched_document_payment_id = $store['id'];
            $received->claimed_by_user_id = auth()->id();
            $received->save();

            return $received;
        });

        return $this->buildResponse(true, 'Pago actualizado con exito', $record);
    }


    /**
     *
     * Descartar un pago pendiente (no se asigna a ningun comprobante).
     *
     * No se permite descartar un pago ya reclamado (409).
     *
     * @param  int $id
     * @return array
     */
    public function discard($id)
    {
        $record = DB::connection('tenant')->transaction(function () use ($id) {

            $received = ReceivedPayment::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($received->isMatched()) {
                $this->throwConflict('No se puede descartar un pago ya reclamado.');
            }

            $received->status = ReceivedPayment::STATUS_DISCARDED;
            $received->save();

            return $received;
        });

        return $this->buildResponse(true, 'Pago descartado con exito', $record);
    }


    /**
     *
     * Ejecuta el registro del pago en el comprobante reutilizando el proceso web.
     *
     * Construye una DocumentPaymentRequest con el monto y la fecha del pago
     * capturado y el metodo/destino del cliente, y delega en el controlador web
     * para reutilizar su logica (pago global, asociacion a caja y procesamiento
     * del credito pendiente).
     *
     * @param  ReceivedPayment $received
     * @param  ReceivedPaymentClaimRequest $request
     * @return array
     */
    private function runDocumentPaymentStore(ReceivedPayment $received, $request)
    {
        $payload = [
            'id' => null,
            'document_id' => (int) $request->input('document_id'),
            'date_of_payment' => $received->posted_at->format('Y-m-d'),
            'payment_method_type_id' => $request->input('payment_method_type_id'),
            'payment_destination_id' => $request->input('payment_destination_id'),
            'payment' => $received->amount,
            'reference' => trim($received->provider.' '.$received->sender_name),
        ];

        $documentPaymentRequest = DocumentPaymentRequest::create('', 'POST', $payload);

        return app(DocumentPaymentControllerWeb::class)->store($documentPaymentRequest);
    }


    /**
     *
     * Elimina el pago del comprobante generado previamente con sus relaciones
     * (cash_document_payments y global_payment), para un reemplazo limpio.
     *
     * @param  ReceivedPayment $received
     * @return void
     */
    private function removeMatchedDocumentPayment(ReceivedPayment $received)
    {
        if (!$received->matched_document_payment_id) {
            return;
        }

        $payment = DocumentPayment::find($received->matched_document_payment_id);

        if ($payment) {
            $payment->cashDocumentPayments()->delete();
            $payment->global_payment()->delete();
            $payment->delete();
        }
    }


    /**
     *
     * Lanza una respuesta de conflicto (transicion de estado no permitida).
     *
     * @param  string $message
     * @param  int $status
     * @return void
     */
    private function throwConflict($message, $status = 409)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $message,
        ], $status));
    }


    /**
     *
     * Estructura estandar de respuesta para un pago recibido.
     *
     * @param  bool $success
     * @param  string $message
     * @param  ReceivedPayment $record
     * @param  array $extra
     * @return array
     */
    private function buildResponse($success, $message, ReceivedPayment $record, array $extra = [])
    {
        return array_merge([
            'success' => $success,
            'message' => $message,
            'data' => $record->getApiRowResource(),
        ], $extra);
    }

}
