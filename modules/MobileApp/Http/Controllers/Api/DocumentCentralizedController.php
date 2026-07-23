<?php

namespace Modules\MobileApp\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Tenant\Document;
use App\Models\Tenant\SaleNote;
use Modules\MobileApp\Http\Resources\Api\DocumentCentralizedCollection;

class DocumentCentralizedController extends Controller
{
    /**
     *
     * Listado unificado de comprobantes con scroll infinito (cursor-based pagination)
     *
     * Enruta internamente según document_type_id:
     *   - '80' → SaleNote (notas de venta)
     *   - '01', '03', etc. → Document (facturas, boletas...)
     *   - omitido → todos los Documents sin filtro de tipo
     *
     * Filtros comunes:
     * - limit: cantidad de registros (máximo 100, default 15)
     * - cursor: posición actual (null en primera petición)
     * - document_type_id: tipo de documento (01, 03, 80...)
     * - series: filtrar por serie exacta
     * - number: búsqueda parcial por número
     * - customer_id: filtrar por cliente
     * - date_start / date_end: rango de fechas (Y-m-d)
     *
     * Filtros exclusivos para Documents (ignorados en SaleNote):
     * - state_type_id: estado del comprobante
     *
     * Filtros exclusivos para SaleNote (ignorados en Documents):
     * - total_canceled: 1=pagado, 0=pendiente
     *
     * @param  Request $request
     * @return array
     */
    public function byScroll(Request $request)
    {
        $limit = min((int) $request->input('limit', 15), 100);
        $cursor = $request->input('cursor');
        $document_type_id = $request->input('document_type_id');

        if ($document_type_id === '80') {
            return $this->saleNotesByScroll($request, $limit, $cursor);
        }

        return $this->documentsByScroll($request, $limit, $cursor, $document_type_id);
    }

    private function documentsByScroll(Request $request, int $limit, ?string $cursor, ?string $document_type_id)
    {
        $query = Document::with(['person', 'user', 'state_type', 'document_type', 'currency_type'])
            ->whereTypeUser()
            ->orderBy('date_of_issue', 'desc')
            ->orderBy('id', 'desc');

        if ($document_type_id) {
            $query->where('document_type_id', $document_type_id);
        }

        if ($request->filled('state_type_id')) {
            $query->where('state_type_id', $request->input('state_type_id'));
        }

        $this->applyCommonFilters($query, $request);

        $records = $cursor
            ? $query->cursorPaginate($limit, ['*'], 'cursor', $cursor)
            : $query->cursorPaginate($limit);

        return $this->buildResponse($records);
    }

    private function saleNotesByScroll(Request $request, int $limit, ?string $cursor)
    {
        $query = SaleNote::with(['person', 'user', 'state_type', 'currency_type'])
            ->whereTypeUser()
            ->orderBy('date_of_issue', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('total_canceled')) {
            $query->where('total_canceled', (bool) $request->input('total_canceled'));
        }

        $this->applyCommonFilters($query, $request);

        $records = $cursor
            ? $query->cursorPaginate($limit, ['*'], 'cursor', $cursor)
            : $query->cursorPaginate($limit);

        return $this->buildResponse($records);
    }

    private function applyCommonFilters($query, Request $request): void
    {
        if ($request->filled('series')) {
            $query->where('series', $request->input('series'));
        }

        if ($request->filled('number')) {
            $query->where('number', 'like', '%' . $request->input('number') . '%');
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->filled('date_start') && $request->filled('date_end')) {
            $query->filterRangeDateOfIssue($request->input('date_start'), $request->input('date_end'));
        }
    }

    private function buildResponse($records): array
    {
        return [
            'success' => true,
            'data' => new DocumentCentralizedCollection($records),
            'pagination' => [
                'next_cursor' => $records->nextCursor()?->encode() ?? null,
                'has_more'    => $records->hasMorePages(),
            ],
        ];
    }
}
