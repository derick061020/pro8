<?php

namespace Modules\WhatsAppBot\Services\Tools;

use App\Models\Tenant\Item;
use App\Models\Tenant\Person;

class CreateDocumentTool implements ToolInterface
{
    private const IGV_RATE = 0.18;
    private const DOC_TYPE_FACTURA = '01';
    private const DOC_TYPE_BOLETA = '03';

    public function name(): string
    {
        return 'create_document';
    }

    public function definition(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $this->name(),
                'description' => 'Prepara un comprobante (boleta o factura) para emisión. NO emite todavía. Devuelve un resumen para que el vendedor confirme.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'document_type' => [
                            'type' => 'string',
                            'enum' => ['boleta', 'factura'],
                            'description' => 'Tipo de comprobante a emitir.',
                        ],
                        'customer_id' => [
                            'type' => ['integer', 'null'],
                            'description' => 'ID del cliente (id de Person). Null solo para boleta a consumidor final sin DNI.',
                        ],
                        'items' => [
                            'type' => 'array',
                            'description' => 'Líneas del comprobante.',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'item_id' => ['type' => 'integer', 'description' => 'ID del Item del catálogo.'],
                                    'quantity' => ['type' => 'number', 'description' => 'Cantidad a vender.'],
                                ],
                                'required' => ['item_id', 'quantity'],
                            ],
                        ],
                        'observations' => [
                            'type' => 'string',
                            'description' => 'Observaciones opcionales para el comprobante.',
                        ],
                        'discount_amount' => [
                            'type' => 'number',
                            'description' => 'Descuento global en soles (monto fijo). Se aplica al total y reduce la base del IGV. Solo úsalo si el vendedor pidió un descuento explícito (ej. "con S/ 9 de descuento", "descuéntale 10 soles"). Debe ser menor al subtotal antes de IGV.',
                        ],
                    ],
                    'required' => ['document_type', 'items'],
                ],
            ],
        ];
    }

    public function execute(array $arguments): array
    {
        $documentType = $arguments['document_type'] ?? null;
        $customerId = $arguments['customer_id'] ?? null;
        $items = $arguments['items'] ?? [];
        $observations = trim((string) ($arguments['observations'] ?? ''));
        $discountAmount = round((float) ($arguments['discount_amount'] ?? 0), 2);

        if (!in_array($documentType, ['boleta', 'factura'], true)) {
            return ['status' => 'error', 'error' => 'document_type debe ser "boleta" o "factura".'];
        }
        if (empty($items)) {
            return ['status' => 'error', 'error' => 'Debes incluir al menos un item.'];
        }

        $documentTypeId = $documentType === 'factura' ? self::DOC_TYPE_FACTURA : self::DOC_TYPE_BOLETA;

        $customer = null;
        if ($customerId) {
            $customer = Person::find($customerId);
            if (!$customer) {
                return ['status' => 'error', 'error' => "Cliente id={$customerId} no encontrado."];
            }
        }

        $lines = [];
        $totalValue = 0.0;
        $totalIgv = 0.0;

        // Mismo metodo que FacturaloDraftMapper::mapItem: el total de la linea
        // se calcula multiplicando el precio unitario CON IGV completo por la
        // cantidad, y recien ahi se separa base/IGV. Redondear el unit_value
        // sin IGV antes de multiplicar (como se hacia antes) arrastra el error
        // de redondeo x cantidad (ver bug: S/3.00 x 15 daba S/44.96 en vez de
        // S/45.00 exacto).
        foreach ($items as $line) {
            $item = Item::find($line['item_id'] ?? null);
            if (!$item) {
                return ['status' => 'error', 'error' => "Item id={$line['item_id']} no existe."];
            }
            // Defensa anti-alucinacion: el LLM a veces pasa item_ids de items
            // placeholder (ej. "Costo de Envio" con precio 0). Rechazamos en vez
            // de armar un draft invalido. El LLM al ver el error reintenta con
            // un id correcto del search_items previo.
            $itemDisplayName = $item->description ?: $item->name;
            if ((float) $item->sale_unit_price <= 0 || empty(trim($itemDisplayName ?? ''))) {
                return [
                    'status' => 'error',
                    'error' => "Item id={$item->id} tiene precio en 0 o sin nombre — no es un producto vendible. Llama search_items primero y usa el item_id correcto del producto que pidio el vendedor.",
                ];
            }
            if ($item->item_type_id !== '01') {
                return [
                    'status' => 'error',
                    'error' => "Item id={$item->id} no es un producto vendible (tipo={$item->item_type_id}). Llama search_items primero.",
                ];
            }
            // El campo `items.name` (UI label "Descripción") es lo que SUNAT
            // imprime en la columna DESCRIPCION del PDF. Si esta vacio, el
            // comprobante sale con la columna en blanco — mal visualmente.
            // Bloqueamos con instruccion clara para el vendedor.
            if (empty(trim($item->name ?? ''))) {
                $shownName = $item->description ?: ('id=' . $item->id);
                return [
                    'status' => 'error',
                    'error' => "El producto \"{$shownName}\" no tiene 'Descripción' en su ficha (campo que sale en el PDF de SUNAT). Edita el producto en Pro8 → completa el campo 'Descripción' y vuelve a intentar.",
                ];
            }
            $qty = (float) ($line['quantity'] ?? 1);
            $unitWithIgv = $item->has_igv
                ? (float) $item->sale_unit_price
                : round($item->sale_unit_price * (1 + self::IGV_RATE), 2);
            $lineTotal = round($unitWithIgv * $qty, 2);
            $lineValue = round($lineTotal / (1 + self::IGV_RATE), 2);
            $lineIgv = round($lineTotal - $lineValue, 2);

            $lines[] = [
                'item_id' => (int) $item->id,
                // Mismo fallback que SearchItemsTool: algunos catálogos tienen
                // populated solo `description` (lo que ve el cliente) y `name`
                // vacío o solo el código. Sin este fallback el summary sale
                // con el item sin nombre ("- 1 x  (S/ 5.90)").
                'item_name' => $item->description ?: $item->name,
                'item_code' => $item->item_code ?: $item->internal_id,
                'quantity' => $qty,
                'unit_price_with_igv' => $unitWithIgv,
                'line_total' => $lineTotal,
                'line_igv' => $lineIgv,
            ];

            $totalValue += $lineValue;
            $totalIgv += $lineIgv;
        }

        $totalValue = round($totalValue, 2);
        $totalIgv = round($totalIgv, 2);
        $subtotal = $totalValue;
        $total = round($totalValue + $totalIgv, 2);

        // Descuento global tipo '02' (SUNAT): afecta la base imponible del IGV.
        // Se aplica sobre el valor neto (sin IGV); el IGV se recalcula despues.
        $discountBase = $subtotal;
        if ($discountAmount > 0) {
            if ($discountAmount >= $discountBase) {
                return [
                    'status' => 'error',
                    'error' => "El descuento (S/ {$discountAmount}) no puede ser mayor o igual al subtotal sin IGV (S/ {$discountBase}).",
                ];
            }
            $subtotal = round($discountBase - $discountAmount, 2);
            $totalIgv = round($subtotal * self::IGV_RATE, 2);
            $total = round($subtotal + $totalIgv, 2);
        }

        $draft = [
            'document_type_id' => $documentTypeId,
            'document_type_label' => $documentType,
            'customer_id' => $customer ? (int) $customer->id : null,
            'customer_identity_document_type_id' => $customer ? $customer->identity_document_type_id : null,
            'customer_document_number' => $customer ? $customer->number : null,
            'customer_name' => $customer ? $customer->name : 'Consumidor final',
            'items' => $lines,
            'subtotal' => $subtotal,
            'igv' => $totalIgv,
            'total' => $total,
            'discount_amount' => $discountAmount,
            'discount_base' => $discountAmount > 0 ? $discountBase : 0.0,
            'observations' => $observations !== '' ? $observations : null,
        ];

        $summary = $this->buildSummaryText($draft);

        return [
            'status' => 'ready_for_confirmation',
            'summary_text' => $summary,
            'draft' => $draft,
            'message_to_seller' => 'Voy a emitir este comprobante. Por favor confirma con "sí" o cancela con "no".',
        ];
    }

    private function buildSummaryText(array $draft): string
    {
        $type = ucfirst($draft['document_type_label']);
        $lines = [];
        $lines[] = "=== {$type} a emitir ===";
        $lines[] = 'Cliente: ' . $draft['customer_name']
            . ($draft['customer_document_number'] ? ' (' . $draft['customer_document_number'] . ')' : '');
        $lines[] = '';
        $lines[] = 'Items:';
        foreach ($draft['items'] as $line) {
            $lines[] = '  - ' . $line['quantity'] . ' x ' . $line['item_name']
                . ' (S/ ' . number_format($line['unit_price_with_igv'], 2) . ')'
                . ' = S/ ' . number_format($line['line_total'], 2);
        }
        $lines[] = '';
        if (!empty($draft['discount_amount']) && $draft['discount_amount'] > 0) {
            $lines[] = 'Subtotal sin descuento: S/ ' . number_format($draft['discount_base'], 2);
            $lines[] = 'Descuento: -S/ ' . number_format($draft['discount_amount'], 2);
        }
        $lines[] = 'Subtotal: S/ ' . number_format($draft['subtotal'], 2);
        $lines[] = 'IGV: S/ ' . number_format($draft['igv'], 2);
        $lines[] = 'Total: S/ ' . number_format($draft['total'], 2);
        if ($draft['observations']) {
            $lines[] = '';
            $lines[] = 'Obs: ' . $draft['observations'];
        }

        return implode("\n", $lines);
    }
}
