<?php

namespace Modules\WhatsAppBot\Services\Validators;

use App\Models\Tenant\Item;
use App\Models\Tenant\Person;

class AntiHallucinationValidator implements DocumentValidator
{
    public function validate(array $draft): ValidationResult
    {
        $items = $draft['items'] ?? [];
        foreach ($items as $idx => $line) {
            $itemId = $line['item_id'] ?? null;
            if (!$itemId || !Item::where('id', $itemId)->exists()) {
                return ValidationResult::fail(
                    "El item de la línea " . ($idx + 1) . " (id={$itemId}) no existe en el catálogo.",
                    'item_not_found'
                );
            }
        }

        $customerId = $draft['customer_id'] ?? null;
        if ($customerId && !Person::where('id', $customerId)->exists()) {
            return ValidationResult::fail("El cliente referenciado (id={$customerId}) no existe.", 'customer_not_found');
        }

        return ValidationResult::ok();
    }
}
