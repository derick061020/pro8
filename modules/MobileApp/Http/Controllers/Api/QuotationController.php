<?php

namespace Modules\MobileApp\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\Tenant\Quotation;
use Modules\MobileApp\Http\Resources\Api\QuotationCollection;


class QuotationController extends Controller
{
    /**
     *
     * Obtener cotizaciones para scroll infinito
     * Se usa cursor-based pagination para mejor rendimiento
     *
     * Parámetros soportados:
     * - limit: cantidad de registros (máximo 100, default 15)
     * - cursor: posición actual (null en primera petición)
     * - state_type_id: filtrar por estado de la cotización (opcional)
     * - changed: filtrar por estado de cambios true/false (opcional)
     *
     * @param  Request $request
     * @return array
     */
    public function byScroll(Request $request)
    {
        $limit = $request->input('limit', 15);
        $cursor = $request->input('cursor');
        $state_type_id = $request->input('state_type_id');
        $changed = $request->input('changed');

        // Validar límite máximo
        $limit = min($limit, 100);

        $query = Quotation::with(['user', 'soap_type', 'state_type', 'currency_type', 'items', 'payments', 'person'])
            ->orderBy('created_at', 'desc');

        // Filtros opcionales
        if ($state_type_id !== null) {
            $query->filterByStateType($state_type_id);
        }

        if ($changed !== null) {
            $query->filterByChanged($changed);
        }

        if ($cursor) {
            $records = $query->cursorPaginate($limit, ['*'], 'cursor', $cursor);
        } else {
            $records = $query->cursorPaginate($limit);
        }

        return [
            'success' => true,
            'data' => new QuotationCollection($records),
            'pagination' => [
                'next_cursor' => $records->nextCursor()?->encode() ?? null,
                'has_more' => $records->hasMorePages(),
            ]
        ];
    }
}