<?php

namespace Modules\Webhook\Services\Payloads;

use App\Models\Tenant\Purchase;

class PurchaseCreatedPayload implements PayloadBuilderInterface
{
    /**
     * @param Purchase $model
     */
    public function build($model): array
    {
        $supplier = $model->supplier;
        if (!$supplier && $model->supplier_id) {
            $person = $model->supplier()->first();
            $supplier = $person ? (object) $person->only(['identity_document_type_id', 'number', 'name']) : null;
        }

        return [
            'id' => $model->id,
            'external_id' => $model->external_id,
            'number' => $model->number_full,
            'date_of_issue' => optional($model->date_of_issue)->format('Y-m-d'),
            'document_type_id' => $model->document_type_id,
            'state_type_id' => $model->state_type_id,
            'currency_type_id' => $model->currency_type_id,
            'exchange_rate_sale' => $model->exchange_rate_sale,
            'total' => $model->total,
            'supplier' => $supplier ? [
                'identity_document_type_id' => $supplier->identity_document_type_id ?? null,
                'number' => $supplier->number ?? null,
                'name' => $supplier->name ?? null,
            ] : null,
            'items_count' => $model->items()->count(),
        ];
    }
}
