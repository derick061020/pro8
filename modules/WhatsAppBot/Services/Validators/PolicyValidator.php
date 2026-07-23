<?php

namespace Modules\WhatsAppBot\Services\Validators;

class PolicyValidator implements DocumentValidator
{
    private const ALLOWED_TYPES = ['01', '03'];
    private const MAX_AMOUNT_BOLETA_NO_DNI = 700.00;

    public function validate(array $draft): ValidationResult
    {
        $type = $draft['document_type_id'] ?? null;
        if (!in_array($type, self::ALLOWED_TYPES, true)) {
            return ValidationResult::fail(
                "Tipo de comprobante no permitido. Solo boleta (03) o factura (01).",
                'invalid_document_type'
            );
        }

        $items = $draft['items'] ?? [];
        if (empty($items)) {
            return ValidationResult::fail('El comprobante no tiene items.', 'no_items');
        }

        $total = (float) ($draft['total'] ?? 0);
        $customerDocument = $draft['customer_document_number'] ?? '';

        if ($type === '03' && empty($customerDocument) && $total > self::MAX_AMOUNT_BOLETA_NO_DNI) {
            return ValidationResult::fail(
                'Boleta sin DNI no puede exceder S/ ' . number_format(self::MAX_AMOUNT_BOLETA_NO_DNI, 2) . '. Pide el DNI al cliente.',
                'boleta_no_dni_amount_exceeded'
            );
        }

        if ($type === '01') {
            $docType = $draft['customer_identity_document_type_id'] ?? null;
            if ($docType !== '6') {
                return ValidationResult::fail('Para factura el cliente debe tener RUC.', 'factura_requires_ruc');
            }
        }

        return ValidationResult::ok();
    }
}
