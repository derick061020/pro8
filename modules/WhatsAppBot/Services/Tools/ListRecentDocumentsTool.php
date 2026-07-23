<?php

namespace Modules\WhatsAppBot\Services\Tools;

use App\Models\Tenant\Document;

class ListRecentDocumentsTool implements ToolInterface
{
    private const MAX_RESULTS = 10;

    public function name(): string
    {
        return 'list_recent_documents';
    }

    public function definition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $this->name(),
                'description' => 'Lista los comprobantes emitidos recientemente. Útil cuando el vendedor pregunta por ventas pasadas o quiere reenviar un PDF. Devuelve número, fecha, cliente, total y estado SUNAT. Cuando el vendedor diga "boletas" filtra por document_type="boleta", cuando diga "facturas" filtra por document_type="factura".',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'limit' => [
                            'type' => 'integer',
                            'description' => 'Cantidad de comprobantes a listar (máximo 10). Si no se indica, devuelve los últimos 5.',
                        ],
                        'document_type' => [
                            'type' => 'string',
                            'enum' => ['boleta', 'factura'],
                            'description' => 'Filtrar solo por boletas o solo por facturas. Si se omite, devuelve ambos tipos.',
                        ],
                        'customer_document' => [
                            'type' => 'string',
                            'description' => 'Filtrar por DNI o RUC del cliente (opcional).',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function execute(array $arguments): array
    {
        $limit = (int) ($arguments['limit'] ?? 5);
        $limit = max(1, min($limit, self::MAX_RESULTS));
        $customerDoc = trim((string) ($arguments['customer_document'] ?? ''));
        $docType = $arguments['document_type'] ?? null;

        $typeIds = match ($docType) {
            'factura' => ['01'],
            'boleta' => ['03'],
            default => ['01', '03'],
        };

        $query = Document::query()
            ->whereIn('document_type_id', $typeIds)
            ->orderByDesc('id')
            ->limit($limit);

        if ($customerDoc !== '') {
            $query->whereHas('person', function ($q) use ($customerDoc) {
                $q->where('number', $customerDoc);
            });
        }

        $documents = $query->get([
            'id', 'document_type_id', 'series', 'number',
            'date_of_issue', 'customer_id', 'total', 'state_type_id',
        ]);

        return [
            'count' => $documents->count(),
            'documents' => $documents->map(fn ($d) => [
                'id' => $d->id,
                'number_full' => $d->series . '-' . $d->number,
                'type' => $d->document_type_id === '01' ? 'factura' : 'boleta',
                'date' => $d->date_of_issue?->format('Y-m-d'),
                'customer_name' => optional($d->person)->name,
                'customer_document' => optional($d->person)->number,
                'total' => (float) $d->total,
                'state' => $this->stateDescription($d->state_type_id),
            ])->all(),
        ];
    }

    private function stateDescription(?string $stateId): string
    {
        return match ($stateId) {
            '01' => 'registrado',
            '05' => 'aceptado',
            '07' => 'rechazado',
            '11' => 'anulado',
            '13' => 'por anular',
            default => 'desconocido',
        };
    }
}
