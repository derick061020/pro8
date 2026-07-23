<?php

namespace Modules\MobileApp\Http\Resources\Api;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QuotationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($quotation) {
            return [
                'id' => $quotation->id,
                'number_full' => $quotation->number_full,
                'filename' => $quotation->filename,
                'external_id' => $quotation->external_id,
                'date_of_issue' => $quotation->date_of_issue?->format('Y-m-d'),
                'total' => (float) $quotation->total,
                'currency_type_id' => $quotation->currency_type_id,
                'state_type_id' => $quotation->state_type_id,
                'state_type_description' => $quotation->state_type?->description,
                'changed' => (bool) $quotation->changed,
                'customer' => [
                    'id' => $quotation->person?->id,
                    'name' => $quotation->person?->name,
                    'number' => $quotation->person?->number,
                ],
                'user' => [
                    'id' => $quotation->user?->id,
                    'name' => $quotation->user?->name,
                ],
                'seller' => [
                    'id' => $quotation->seller?->id,
                    'name' => $quotation->seller?->name ?? 'Sin asignar',
                ],
                'items_count' => $quotation->items?->count() ?? 0,
                'has_documents' => $quotation->hasAcceptedDocuments(),
                'can_generate_document' => !$quotation->hasAcceptedDocuments() && count($quotation->sale_notes) === 0,
                'print_a4' => $quotation->getUrlPrintPdf('a4'),
                'print_ticket' => $quotation->getUrlPrintPdf('ticket'),
                'created_at' => $quotation->created_at?->format('Y-m-d H:i:s'),
            ];
        });
    }
}
