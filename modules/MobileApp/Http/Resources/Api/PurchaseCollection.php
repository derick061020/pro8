<?php

namespace Modules\MobileApp\Http\Resources\Api;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PurchaseCollection extends ResourceCollection
{
    /**
     *
     * Transformar el listado de compras para scroll infinito en la app
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($purchase) {

            $supplier = $purchase->supplier;

            return [
                'id'                        => $purchase->id,
                'external_id'               => $purchase->external_id,
                'document_type_id'          => $purchase->document_type_id,
                'document_type_description' => $purchase->document_type?->description,
                'series'                    => $purchase->series,
                'number'                    => $purchase->number,
                'number_full'               => $purchase->number_full,
                'filename'                  => $purchase->filename,
                'date_of_issue'             => $purchase->date_of_issue?->format('Y-m-d'),
                'date_of_due'               => $purchase->date_of_due?->format('Y-m-d'),
                'time_of_issue'             => $purchase->time_of_issue,
                'currency_type_id'          => $purchase->currency_type_id,
                'exchange_rate_sale'        => (float) $purchase->exchange_rate_sale,
                'total_taxed'               => (float) $purchase->total_taxed,
                'total_exonerated'          => (float) $purchase->total_exonerated,
                'total_unaffected'          => (float) $purchase->total_unaffected,
                'total_free'                => (float) $purchase->total_free,
                'total_igv'                 => (float) $purchase->total_igv,
                'total_isc'                 => (float) $purchase->total_isc,
                'total_perception'          => (float) $purchase->total_perception,
                'total'                     => (float) $purchase->total,
                'total_canceled'            => (bool) $purchase->total_canceled,
                'state_type_id'             => $purchase->state_type_id,
                'state_type_description'    => $purchase->state_type?->description,
                'state_type_payment_description' => $purchase->total_canceled ? 'Pagado' : 'Pendiente de pago',
                'supplier' => [
                    'id'     => $supplier?->id ?? $purchase->supplier_id,
                    'name'   => $supplier?->name,
                    'number' => $supplier?->number,
                    'identity_document_type_id' => $supplier?->identity_document_type_id ?? null,
                    'address' => $supplier?->address ?? null,
                    'email'  => $supplier?->email ?? null,
                    'telephone' => $supplier?->telephone ?? null,
                ],
                'user' => [
                    'id'   => $purchase->user?->id,
                    'name' => $purchase->user?->name,
                ],
                'items_count' => $purchase->items?->count() ?? 0,
                'print_a4'    => $purchase->getUrlPrintPdf('a4'),
                'print_ticket'    => $purchase->getUrlPrintPdf('ticket'),
            ];
        });
    }
}
