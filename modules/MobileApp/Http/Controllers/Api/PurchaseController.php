<?php

namespace Modules\MobileApp\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\Item;
use App\Models\Tenant\Person;
use Modules\MobileApp\Http\Resources\Api\PurchaseCollection;

class PurchaseController extends Controller
{
    /**
     *
     * Obtener compras para scroll infinito
     * Se usa cursor-based pagination para mejor rendimiento
     *
     * Parámetros soportados:
     * - limit: cantidad de registros (máximo 100, default 15)
     * - cursor: posición actual (null en primera petición)
     * - document_type_id: filtrar por tipo de documento (01, 03, GU75, NE76)
     * - state_type_id: filtrar por estado de la compra
     * - series: filtrar por serie exacta
     * - number: búsqueda parcial por número
     * - supplier_id: filtrar por proveedor
     * - date_start / date_end: rango de fechas (Y-m-d) sobre date_of_issue
     * - total_canceled: 1=pagado, 0=pendiente
     *
     * @param  Request $request
     * @return array
     */
    public function byScroll(Request $request)
    {
        $limit  = min((int) $request->input('limit', 15), 100);
        $cursor = $request->input('cursor');

        $query = Purchase::with([
                'user',
                'soap_type',
                'state_type',
                'document_type',
                'currency_type',
                'items',
                'purchase_payments',
            ])
            ->whereTypeUser()
            ->orderBy('date_of_issue', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->input('document_type_id'));
        }

        if ($request->filled('state_type_id')) {
            $query->where('state_type_id', $request->input('state_type_id'));
        }

        if ($request->filled('series')) {
            $query->where('series', $request->input('series'));
        }

        if ($request->filled('number')) {
            $query->where('number', 'like', '%'.$request->input('number').'%');
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('date_start') && $request->filled('date_end')) {
            $query->whereBetween('date_of_issue', [
                $request->input('date_start'),
                $request->input('date_end'),
            ]);
        }

        if ($request->filled('total_canceled')) {
            $query->where('total_canceled', (bool) $request->input('total_canceled'));
        }

        $records = $cursor
            ? $query->cursorPaginate($limit, ['*'], 'cursor', $cursor)
            : $query->cursorPaginate($limit);

        return [
            'success'    => true,
            'data'       => new PurchaseCollection($records),
            'pagination' => [
                'next_cursor' => $records->nextCursor()?->encode() ?? null,
                'has_more'    => $records->hasMorePages(),
            ],
        ];
    }


    /**
     *
     * Listado de items para el formulario de compras con cursor-based pagination
     *
     * Reemplazo de `purchases/item-tables` (que devolvía todo de una sola vez).
     * Retorna el mismo shape que el original para mantener compatibilidad con
     * el formulario de compras existente. No incluye affectation_igv_types
     * (idéntico criterio que en el endpoint de items para mobile).
     *
     * Parámetros soportados:
     * - limit: cantidad de registros (máximo 100, default 15)
     * - cursor: posición actual (null en primera petición)
     * - input: búsqueda por descripción o internal_id
     *
     * @param  Request $request
     * @return array
     */
    // public function itemsByScroll(Request $request)
    // {
    //     $limit  = min((int) $request->input('limit', 15), 100);
    //     $cursor = $request->input('cursor');
    //     $input  = $request->input('input');

    //     $query = Item::whereNotIsSet()
    //         ->whereIsActive()
    //         ->orderBy('description', 'asc');

    //     if (!empty($input)) {
    //         $query->where(function ($q) use ($input) {
    //             $q->where('description', 'like', "%{$input}%")
    //               ->orWhere('internal_id', 'like', "%{$input}%")
    //               ->orWhere('barcode', 'like', "%{$input}%");
    //         });
    //     }

    //     $records = $cursor
    //         ? $query->cursorPaginate($limit, ['*'], 'cursor', $cursor)
    //         : $query->cursorPaginate($limit);

    //     $data = $records->getCollection()->transform(function ($row) {
    //         $full_description = $row->internal_id
    //             ? $row->internal_id.' - '.$row->description
    //             : $row->description;

    //         return [
    //             'id'                               => $row->id,
    //             'item_code'                        => $row->item_code,
    //             'full_description'                 => $full_description,
    //             'description'                      => $row->description,
    //             'currency_type_id'                 => $row->currency_type_id,
    //             'currency_type_symbol'             => $row->currency_type?->symbol,
    //             'sale_unit_price'                  => $row->sale_unit_price,
    //             'purchase_unit_price'              => $row->purchase_unit_price,
    //             'unit_type_id'                     => $row->unit_type_id,
    //             'sale_affectation_igv_type_id'     => $row->sale_affectation_igv_type_id,
    //             'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
    //             'has_perception'                   => (bool) $row->has_perception,
    //             'lots_enabled'                     => (bool) $row->lots_enabled,
    //             'percentage_perception'            => $row->percentage_perception,
    //         ];
    //     });

    //     return [
    //         'success'    => true,
    //         'data'       => $data,
    //         'pagination' => [
    //             'next_cursor' => $records->nextCursor()?->encode() ?? null,
    //             'has_more'    => $records->hasMorePages(),
    //         ],
    //     ];
    // }


    /**
     *
     * Listado unificado de proveedores con cursor-based pagination.
     * Reemplaza los endpoints `purchases/suppliers` y `purchases/search-suppliers`
     * (ambos sin paginación). El shape del row es idéntico al existente para no
     * romper el formulario de compras.
     *
     * Parámetros soportados:
     * - limit: cantidad de registros (máximo 100, default 15)
     * - cursor: posición actual (null en primera petición)
     * - input: búsqueda por número o nombre
     * - document_type_id: tipo de comprobante a emitir; '01' (factura) restringe
     *   identity_document_type_id a [6] (RUC), cualquier otro a [1,4,6,7,0]
     *
     * @param  Request $request
     * @return array
     */
    public function suppliersByScroll(Request $request)
    {
        $limit  = min((int) $request->input('limit', 15), 100);
        $cursor = $request->input('cursor');
        $input  = $request->input('input');

        $query = Person::whereType('suppliers')->orderBy('name', 'asc');

        if ($request->filled('document_type_id')) {
            $identity_document_type_id = ($request->input('document_type_id') == '01') ? [6] : [1, 4, 6, 7, 0];
            $query->whereIn('identity_document_type_id', $identity_document_type_id);
        }

        if (!empty($input)) {
            $query->where(function ($q) use ($input) {
                $q->where('number', 'like', "%{$input}%")
                  ->orWhere('name', 'like', "%{$input}%");
            });
        }

        $records = $cursor
            ? $query->cursorPaginate($limit, ['*'], 'cursor', $cursor)
            : $query->cursorPaginate($limit);

        $data = $records->getCollection()->transform(function ($row) {
            return [
                'id'                        => $row->id,
                'description'               => $row->number.' - '.$row->name,
                'name'                      => $row->name,
                'number'                    => $row->number,
                'identity_document_type_id' => $row->identity_document_type_id,
                'address'                   => $row->address,
                'email'                     => $row->email,
                'selected'                  => false,
            ];
        });

        return [
            'success'    => true,
            'data'       => $data,
            'pagination' => [
                'next_cursor' => $records->nextCursor()?->encode() ?? null,
                'has_more'    => $records->hasMorePages(),
            ],
        ];
    }
}
