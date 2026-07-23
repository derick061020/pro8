<?php

namespace Modules\MobileApp\Http\Resources\Api;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Tenant\SaleNote;

class DocumentCentralizedCollection extends ResourceCollection
{
    private function resolveHasCdr($document): bool
    {
        if ($document->group_id === '01') {
            return $document->state_type_id === '05';
        }

        if ($document->group_id === '02') {
            return $document->state_type_id === '05' && $document->isSingleDocumentShipment();
        }

        return false;
    }

    public function toArray($request)
    {
        return $this->collection->map(function ($record) {
            $is_sale_note = $record instanceof SaleNote;

            return [
                'id'                        => $record->id,
                'external_id'               => $record->external_id,
                'document_type_id'          => $is_sale_note ? '80' : $record->document_type_id,
                'document_type_description' => $is_sale_note ? 'Nota de Venta' : $record->document_type?->description,
                'number_full'               => $record->number_full,
                'series'                    => $record->series,
                'number'                    => $record->number,
                'filename'                  => $record->filename,
                'date_of_issue'             => $record->date_of_issue?->format('Y-m-d'),
                'total'                     => (float) $record->total,
                'currency_type_id'          => $record->currency_type_id,
                'state_type_id'             => $record->state_type_id,
                'state_type_description'    => $record->state_type?->description,
                'customer' => [
                    'id'     => $record->person?->id,
                    'name'   => $record->person?->name,
                    'number' => $record->person?->number,
                    'email' => $record->person?->email,
                    'phone' => $record->person?->telephone,
                ],
                'user' => [
                    'id'   => $record->user?->id,
                    'name' => $record->user?->name,
                ],
                'print_a4'     => $is_sale_note
                    ? $record->getUrlPrintPdf('a4')
                    : $record->getUrlPrintByFormat('a4'),
                'print_ticket' => $is_sale_note
                    ? $record->getUrlPrintPdf('ticket')
                    : $record->getUrlPrintByFormat('ticket'),
                'has_cdr'      => !$is_sale_note && $this->resolveHasCdr($record),
                'download_cdr' => !$is_sale_note && $this->resolveHasCdr($record)
                    ? $record->download_external_cdr
                    : null,
                'created_at'   => $record->created_at?->format('Y-m-d H:i:s'),
            ];
        });
    }
}
