<?php

namespace Modules\MobileApp\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Cash;
use Modules\MobileApp\Http\Resources\Api\{
	CashCollection,
	CashResource,
};
use App\Http\Controllers\Tenant\CashController as CashControllerWeb;
use Modules\Pos\Http\Controllers\CashController as CashControllerWebPos;
use Modules\Report\Http\Controllers\ReportIncomeSummaryController;
use App\Http\Requests\Tenant\CashRequest;

class CashController extends Controller
{


    /**
     *
     * Obtener caja
     *
     * @param  int $id
     * @return array
     */
    public function record($id)
    {
        return app(CashControllerWeb::class)->record($id);
    }


    /**
     *
     * Registrar/Actualizar caja
     *
     * @param  CashRequest $request
     * @return array
     */
    public function store(CashRequest $request)
    {
        if(!$request['user_id'])
        {
            $request['user_id'] = auth()->id();
        }

        return app(CashControllerWeb::class)->store($request);
    }


    /**
     *
     * Validar si el usuario tiene caja aperturada
     *
     * @return array
     */
    public function checkOpenCash()
    {
        $data = app(CashControllerWeb::class)->opening_cash_check(auth()->id());

        if($data['cash']) return $this->generalResponse(true, 'No puede crear caja chica, por favor cierre caja chica para el usuario definido');

        return $this->generalResponse(false);
    }


    /**
     *
     * Obtener registros paginados
     *
     * @param  Request $request
     * @return array
     */
	public function records(Request $request)
	{
        $records = Cash::whereFilterRecordsApi($request->input);

		return new CashCollection($records->paginate(config('tenant.items_per_page')));
	}


    /**
     *
     * Obtener registros para scroll infinito
     * Se usa cursor-based pagination para mejor rendimiento
     *
     * Parámetros soportados:
     * - limit: cantidad de registros (máximo 100, default 15)
     * - cursor: posición actual (null en primera petición)
     * - input: búsqueda por income o reference_number
     * - state: filtrar por estado (true=aperturada, false=cerrada) opcional
     * - date_start: fecha inicio rango (Y-m-d) sobre date_opening
     * - date_end: fecha fin rango (Y-m-d) sobre date_opening
     *
     * @param  Request $request
     * @return array
     */
    public function byScroll(Request $request)
    {
        $limit = $request->input('limit', config('tenant.items_per_page', 15));
        $cursor = $request->input('cursor');
        $input = $request->input('input', '');
        $state = $request->input('state');

        $limit = min((int) $limit, 100);

        $query = Cash::whereFilterRecordsApi($input)
            ->orderBy('id', 'desc');

        if ($state !== null) {
            $query->where('state', filter_var($state, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('date_start') && $request->filled('date_end')) {
            $query->whereBetween('date_opening', [
                $request->input('date_start'),
                $request->input('date_end'),
            ]);
        }

        if ($cursor) {
            $records = $query->cursorPaginate($limit, ['*'], 'cursor', $cursor);
        } else {
            $records = $query->cursorPaginate($limit);
        }

        return [
            'data' => CashCollection::make($records),
            'pagination' => [
                'next_cursor' => $records->nextCursor()?->encode() ?? null,
                'has_more' => $records->hasMorePages(),
            ]
        ];
    }


    /**
     *
     * Cerrar caja, usa método del proceso por web
     *
     * @param  int $id
     * @return array
     */
    public function close($id)
    {
        return app(CashControllerWeb::class)->close($id);
    }


    /**
     *
     * Eliminar registro, usa método del proceso por web
     *
     * @param  int $id
     * @return array
     */
    public function destroy($id)
    {
        return app(CashControllerWeb::class)->destroy($id);
    }


    /**
     *
     * Envio de email
     *
     * @param  Request $request
     * @return array
     */
    public function email(Request $request)
    {
        $request->validate(
            ['email' => 'required']
        );

        return app(CashControllerWebPos::class)->email($request);
    }


    /**
     *
     * Reporte general de caja, usa método del proceso por web
     *
     * @param  int $id
     * @param  string $format
     * @return mixed
     */
    public function generalReport($id, $format = 'a4')
    {

        if($format == 'ticket')
        {
            return app(CashControllerWebPos::class)->reportTicket($id, 80);
        }

        return app(CashControllerWebPos::class)->reportA4Api($id);
    }


    /**
     *
     * Reporte de productos
     *
     * @param  int $id
     * @return mixed
     */
    public function productReport($id)
    {
        return app(CashControllerWeb::class)->report_products($id);
    }


    /**
     *
     * Reporte de ingresos y egresos en efectivo con destino caja
     *
     * @param  int $id
     * @return mixed
     */
    public function incomeEgressReport($id)
    {
        return app(CashControllerWebPos::class)->reportCashIncomeEgress($id);
    }


    /**
     *
     * Reporte de ingresos
     *
     * @param  int $id
     * @return mixed
     */
    public function incomeSummaryReport($id)
    {
        return app(ReportIncomeSummaryController::class)->pdf($id);
    }


    /**
     *
     * Asociar documento a caja
     *
     * @param  Request $request
     * @return array
     */
    public function storeCashDocument(Request $request)
    {

        $request->validate([
            'document_id' => 'required_if:sale_note_id, ""',
            'sale_note_id' => 'required_if:document_id, ""',
        ]);

        return app(CashControllerWeb::class)->cash_document($request);

    }


}
