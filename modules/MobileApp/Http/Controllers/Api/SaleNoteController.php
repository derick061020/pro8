<?php

namespace Modules\MobileApp\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Tenant\SaleNote;
use App\Http\Resources\Tenant\SaleNoteCollection;

class SaleNoteController extends Controller
{
    /**
     *
     * Listado de notas de venta con scroll infinito (cursor-based pagination)
     *
     * Parámetros soportados:
     * - limit: cantidad de registros (máximo 100, default 15)
     * - cursor: posición actual (null en primera petición)
     * - series: filtrar por serie exacta (NV01, T001...)
     * - number: buscar por número (búsqueda parcial)
     * - customer_id: filtrar por cliente
     * - total_canceled: 1=pagado, 0=pendiente
     * - date_start: fecha inicio rango (Y-m-d)
     * - date_end: fecha fin rango (Y-m-d)
     *
     * @param  Request $request
     * @return array
     */
    public function byScroll(Request $request)
    {
        $limit = min((int) $request->input('limit', 15), 100);
        $cursor = $request->input('cursor');

        $query = SaleNote::whereTypeUser()
            ->orderBy('date_of_issue', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('series')) {
            $query->where('series', $request->input('series'));
        }

        if ($request->filled('number')) {
            $query->where('number', 'like', '%' . $request->input('number') . '%');
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->filled('total_canceled')) {
            $query->where('total_canceled', (bool) $request->input('total_canceled'));
        }

        if ($request->filled('date_start') && $request->filled('date_end')) {
            $query->filterRangeDateOfIssue($request->input('date_start'), $request->input('date_end'));
        }

        $records = $cursor
            ? $query->cursorPaginate($limit, ['*'], 'cursor', $cursor)
            : $query->cursorPaginate($limit);

        return [
            'success' => true,
            'data' => new SaleNoteCollection($records),
            'pagination' => [
                'next_cursor' => $records->nextCursor()?->encode() ?? null,
                'has_more'    => $records->hasMorePages(),
            ],
        ];
    }
}
