<?php

namespace Modules\Webhook\Services;

use Modules\Webhook\Services\Payloads\DocumentPayload;
use Modules\Webhook\Services\Payloads\PayloadBuilderInterface;
use Modules\Webhook\Services\Payloads\PurchaseCreatedPayload;

/**
 * Registro central de eventos de webhook.
 *
 * Para añadir un nuevo evento ver modules/Webhook/README.md
 * (constante + entrada en $map; si depende de un estado SUNAT,
 * una línea más en $documentStateMap).
 */
class WebhookEvents
{
    const DOCUMENT_CREATED = 'document.created';
    const DOCUMENT_ACCEPTED = 'document.accepted';
    const DOCUMENT_OBSERVED = 'document.observed';
    const DOCUMENT_REJECTED = 'document.rejected';
    const DOCUMENT_VOIDED = 'document.voided';
    const PURCHASE_CREATED = 'purchase.created';

    private static array $map = [
        self::DOCUMENT_CREATED => [
            'label' => 'Documento de venta emitido',
            'payload' => DocumentPayload::class,
        ],
        self::DOCUMENT_ACCEPTED => [
            'label' => 'Documento aceptado por SUNAT (CDR)',
            'payload' => DocumentPayload::class,
        ],
        self::DOCUMENT_OBSERVED => [
            'label' => 'Documento observado por SUNAT',
            'payload' => DocumentPayload::class,
        ],
        self::DOCUMENT_REJECTED => [
            'label' => 'Documento rechazado por SUNAT',
            'payload' => DocumentPayload::class,
        ],
        self::DOCUMENT_VOIDED => [
            'label' => 'Documento anulado (baja aceptada)',
            'payload' => DocumentPayload::class,
        ],
        self::PURCHASE_CREATED => [
            'label' => 'Compra registrada',
            'payload' => PurchaseCreatedPayload::class,
        ],
    ];

    /**
     * Mapa state_type_id (Facturalo) => evento. SENT '03' es tránsito
     * interno y no se notifica.
     */
    private static array $documentStateMap = [
        '05' => self::DOCUMENT_ACCEPTED,
        '07' => self::DOCUMENT_OBSERVED,
        '09' => self::DOCUMENT_REJECTED,
        '11' => self::DOCUMENT_VOIDED,
    ];

    /**
     * Lista para los checkboxes de la UI.
     *
     * @return array [['value' => ..., 'label' => ...], ...]
     */
    public static function all(): array
    {
        return collect(self::$map)
            ->map(fn ($item, $event) => ['value' => $event, 'label' => $item['label']])
            ->values()
            ->all();
    }

    /**
     * @return string[] nombres de eventos válidos (para validación)
     */
    public static function keys(): array
    {
        return array_keys(self::$map);
    }

    public static function label(string $event): ?string
    {
        return self::$map[$event]['label'] ?? null;
    }

    public static function payloadBuilder(string $event): PayloadBuilderInterface
    {
        return app(self::$map[$event]['payload']);
    }

    public static function forDocumentState(?string $stateTypeId): ?string
    {
        return self::$documentStateMap[$stateTypeId] ?? null;
    }
}
