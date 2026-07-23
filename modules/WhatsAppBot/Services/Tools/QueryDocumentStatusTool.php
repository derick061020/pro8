<?php

namespace Modules\WhatsAppBot\Services\Tools;

use App\Models\Tenant\Document;

class QueryDocumentStatusTool implements ToolInterface
{
    public function name(): string
    {
        return 'query_document_status';
    }

    public function definition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $this->name(),
                'description' => 'Consulta el estado SUNAT de un comprobante específico (aceptado, registrado, rechazado, anulado). Útil cuando el vendedor pregunta si la SUNAT ya aceptó una boleta o factura.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'number_full' => [
                            'type' => 'string',
                            'description' => 'Serie y número del comprobante (ej. B001-9, F001-15).',
                        ],
                        'document_id' => [
                            'type' => 'integer',
                            'description' => 'Alternativa: ID interno del Document (si lo conoces).',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function execute(array $arguments): array
    {
        $document = null;

        if (!empty($arguments['document_id'])) {
            $document = Document::find((int) $arguments['document_id']);
        }

        if (!$document && !empty($arguments['number_full'])) {
            $parts = explode('-', strtoupper(trim($arguments['number_full'])));
            if (count($parts) === 2) {
                [$series, $number] = $parts;
                $document = Document::where('series', $series)
                    ->where('number', (int) ltrim($number, '0'))
                    ->first();
            }
        }

        if (!$document) {
            return ['status' => 'error', 'error' => 'No se encontró el comprobante.'];
        }

        return [
            'number_full' => $document->series . '-' . $document->number,
            'type' => $document->document_type_id === '01' ? 'factura' : 'boleta',
            'date' => $document->date_of_issue?->format('Y-m-d'),
            'total' => (float) $document->total,
            'sunat_state' => $this->stateDescription($document->state_type_id),
            'state_code' => $document->state_type_id,
            'has_pdf' => (bool) $document->has_pdf,
            'has_xml' => (bool) $document->has_xml,
            'has_cdr' => (bool) $document->has_cdr,
        ];
    }

    private function stateDescription(?string $stateId): string
    {
        return match ($stateId) {
            '01' => 'registrado en el sistema (aún no enviado a SUNAT)',
            '03' => 'enviado a SUNAT, esperando respuesta',
            '05' => 'aceptado por SUNAT',
            '07' => 'rechazado por SUNAT',
            '09' => 'enviado con observaciones',
            '11' => 'anulado',
            '13' => 'por anular',
            default => 'estado desconocido',
        };
    }
}
