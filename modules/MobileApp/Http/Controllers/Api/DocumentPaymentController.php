<?php

namespace Modules\MobileApp\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\DocumentPaymentRequest;
use App\Http\Controllers\Tenant\DocumentPaymentController as DocumentPaymentControllerWeb;

class DocumentPaymentController extends Controller
{

    /**
     *
     * Tablas necesarias para registrar un pago
     *
     * Devuelve metodos de pago, destinos de pago (cuentas bancarias / caja)
     * y permisos del usuario en sesion
     *
     * @return array
     */
    public function tables()
    {
        return app(DocumentPaymentControllerWeb::class)->tables();
    }


    /**
     *
     * Obtener saldo del comprobante (total, pagado y diferencia)
     *
     * Permite a la app validar cuanto resta por pagar antes de registrar
     *
     * @param  int $document_id
     * @return array
     */
    public function document($document_id)
    {
        return app(DocumentPaymentControllerWeb::class)->document($document_id);
    }


    /**
     *
     * Listado de pagos registrados de un comprobante
     *
     * @param  int $document_id
     * @return \App\Http\Resources\Tenant\DocumentPaymentCollection
     */
    public function records($document_id)
    {
        return app(DocumentPaymentControllerWeb::class)->records($document_id);
    }


    /**
     *
     * Registrar/Actualizar un pago de un comprobante (factura, boleta)
     *
     * Reutiliza el proceso web: crea el pago global, asocia el pago a la caja
     * aperturada y procesa el credito pendiente cuando el comprobante queda cancelado
     *
     * Parametros requeridos:
     * - document_id: comprobante al que se asigna el pago
     * - date_of_payment: fecha del pago (Y-m-d)
     * - payment_method_type_id: metodo de pago
     * - payment_destination_id: destino del pago (id de cuenta bancaria o 'cash')
     * - payment: monto del pago (> 0)
     *
     * Parametros opcionales:
     * - id: para editar un pago existente
     * - reference: referencia del pago
     *
     * @param  DocumentPaymentRequest $request
     * @return array
     */
    public function store(DocumentPaymentRequest $request)
    {
        $request->validate([
            'document_id' => 'required|integer|exists:tenant.documents,id',
        ]);

        return app(DocumentPaymentControllerWeb::class)->store($request);
    }


    /**
     *
     * Eliminar un pago de un comprobante
     *
     * @param  int $id
     * @return array
     */
    public function destroy($id)
    {
        return app(DocumentPaymentControllerWeb::class)->destroy($id);
    }

}
