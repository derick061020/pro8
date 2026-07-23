<?php

namespace Modules\WhatsAppBot\Services\Facturalo;

use App\Models\Tenant\Item;
use App\Models\Tenant\Person;
use App\Models\Tenant\Series;
use App\Services\SeriesResolver;
use App\Models\Tenant\Document;
use App\Models\Tenant\User;
use Carbon\Carbon;

class FacturaloDraftMapper
{
    private const IGV_RATE = 0.18;
    private const AFFECTATION_TAXED = '10';
    private const SYSTEM_ISC_NONE = '01';
    private const PRICE_TYPE_UNIT = '01';
    private const PAYMENT_METHOD_CASH = '01';

    public function map(array $draft, User $user): array
    {
        $documentTypeId = $draft['document_type_id'];
        $establishmentId = (int) $user->establishment_id;

        // Emisión automática (bot): siempre se omiten series dedicadas; no hay lectura de grupo/dispositivo.
        $series = app(SeriesResolver::class)->applyContext(Series::query()
            ->where('document_type_id', $documentTypeId)
            ->where('establishment_id', $establishmentId)
            ->where('contingency', false), null)
            ->first();

        if (!$series) {
            throw new \RuntimeException("No hay serie configurada para el establishment {$establishmentId} y document_type {$documentTypeId}.");
        }

        $nextNumber = $this->nextNumber($series);

        $customer = $draft['customer_id']
            ? Person::find($draft['customer_id'])
            : $this->getOrCreateGenericConsumer();

        $today = Carbon::now();

        $mappedItems = [];
        $totalTaxed = 0.0;
        $totalIgv = 0.0;
        $totalValue = 0.0;

        foreach ($draft['items'] as $line) {
            $item = Item::find($line['item_id']);
            $mappedItem = $this->mapItem($item, (float) $line['quantity']);
            $mappedItems[] = $mappedItem;
            $totalTaxed += $mappedItem['total_value'];
            $totalIgv += $mappedItem['total_igv'];
            $totalValue += $mappedItem['total_value'];
        }

        $totalTaxed = round($totalTaxed, 2);
        $totalIgv = round($totalIgv, 2);
        $total = round($totalTaxed + $totalIgv, 2);
        $totalValueRounded = round($totalValue, 2);

        // Descuento global tipo '02': afecta la base imponible del IGV.
        // El front (mixins/functions.js::discountGlobal) calcula los mismos
        // ajustes: total_taxed = base - amount, total_igv = nuevo total_taxed * %igv.
        $discountAmount = round((float) ($draft['discount_amount'] ?? 0), 2);
        $discounts = [];
        $totalDiscount = 0.0;
        if ($discountAmount > 0) {
            $discountBase = $totalTaxed;
            $factor = $discountBase > 0 ? round($discountAmount / $discountBase, 5) : 0;
            $totalTaxed = round($discountBase - $discountAmount, 2);
            $totalValueRounded = $totalTaxed;
            $totalIgv = round($totalTaxed * self::IGV_RATE, 2);
            $total = round($totalTaxed + $totalIgv, 2);
            $totalDiscount = $discountAmount;
            $discounts[] = [
                'discount_type_id' => '02',
                'description' => 'Descuentos globales que afectan la base imponible del IGV/IVAP',
                'factor' => $factor,
                'amount' => $discountAmount,
                'amount_without_rounded' => $discountAmount,
                'base' => $discountBase,
                'is_amount' => true,
            ];
        }

        $payload = [
            'type' => 'invoice',
            'user_id' => $user->id,
            'external_id' => (string) \Illuminate\Support\Str::uuid(),
            'establishment_id' => $establishmentId,
            'establishment' => $this->establishmentSnapshot($establishmentId),
            'group_id' => '01',
            'soap_type_id' => '02',
            'state_type_id' => '01',
            'ubl_version' => '2.1',
            'document_type_id' => $documentTypeId,
            'series' => $series->number,
            'number' => $nextNumber,
            'date_of_issue' => $today->format('Y-m-d'),
            'time_of_issue' => $today->format('H:i:s'),
            'customer_id' => $customer?->id,
            'customer' => $this->customerSnapshot($customer),
            'currency_type_id' => 'PEN',
            'exchange_rate_sale' => 1,
            'total_prepayment' => 0,
            'total_discount' => $totalDiscount,
            'total_charge' => 0,
            'total_exportation' => 0,
            'total_free' => 0,
            'total_taxed' => $totalTaxed,
            'total_unaffected' => 0,
            'total_exonerated' => 0,
            'total_igv' => $totalIgv,
            'total_base_isc' => 0,
            'total_isc' => 0,
            'total_base_other_taxes' => 0,
            'total_other_taxes' => 0,
            'total_taxes' => $totalIgv,
            'total_value' => $totalValueRounded,
            'total' => $total,
            'subtotal' => $total,
            'operation_type_id' => '0101',
            'items' => $mappedItems,
            'charges' => [],
            'discounts' => $discounts,
            'attributes' => [],
            'guides' => [],
            'related' => [],
            'perception' => null,
            'detraction' => null,
            'legends' => [],
            'additional_information' => $draft['observations'] ?? null,
            'payment_method_type_id' => '01',
            'payments' => [
                [
                    'date_of_payment' => $today->format('Y-m-d'),
                    'payment_method_type_id' => self::PAYMENT_METHOD_CASH,
                    'payment_destination_id' => null,
                    'reference' => null,
                    'payment' => $total,
                ],
            ],
            'fee' => [],
            'invoice' => [
                'date_of_due' => $today->format('Y-m-d'),
                'operation_type_id' => '0101',
                'financial_condition_type_id' => '01',
                'transfer_detail' => null,
                'transport_detail' => null,
                'related_documents' => [],
            ],
            'hotel' => null,
            'transport' => null,
            'send_server' => true,
            'has_xml' => false,
            'has_pdf' => false,
            'has_cdr' => false,
            'actions' => [
                'send_to_pse' => false,
                'format_pdf' => 'ticket',
            ],
        ];

        return $payload;
    }

    private function mapItem(Item $item, float $quantity): array
    {
        $unitPriceWithIgv = $item->has_igv
            ? (float) $item->sale_unit_price
            : round($item->sale_unit_price * (1 + self::IGV_RATE), 2);

        // Multiplicar primero con el precio unitario CON IGV completo, y recien
        // ahi separar base/IGV. Redondear el unit_value sin IGV antes de
        // multiplicar (como se hacia antes) arrastra el error de redondeo x
        // cantidad (ver bug: S/3.00 x 15 daba S/44.96 en vez de S/45.00 exacto).
        $total = round($unitPriceWithIgv * $quantity, 2);
        $totalValue = round($total / (1 + self::IGV_RATE), 2);
        $totalIgv = round($total - $totalValue, 2);
        // unit_value recalculado desde el total ya reconciliado, en 6 decimales
        // (misma precision que la columna sale_unit_price y que el calculo real
        // del POS en functions.js, que tampoco redondea unit_value a 2
        // decimales). A 2 decimales, unit_value x cantidad no siempre reconcilia
        // con total_value cuando la division no cae exacta (ej. 38.14/15).
        $unitValueWithoutIgv = $quantity > 0 ? round($totalValue / $quantity, 6) : 0.0;

        return [
            'item_id' => $item->id,
            'item' => array_merge($item->toArray(), [
                'description' => $item->name,
                'unit_type_id' => $item->unit_type_id ?: 'NIU',
                'currency_type_id' => $item->currency_type_id ?: 'PEN',
                'has_igv' => (bool) $item->has_igv,
                'calculate_quantity' => false,
                'percentage_igv' => self::IGV_RATE * 100,
                'amount_plastic_bag_taxes' => 0,
                'is_set' => (bool) $item->is_set,
                'IdLoteSelected' => null,
                'lots' => [],
                'presentation' => null,
                'used_points_for_exchange' => null,
                'exchanged_for_points' => false,
            ]),
            'quantity' => $quantity,
            'unit_value' => $unitValueWithoutIgv,
            'price_type_id' => self::PRICE_TYPE_UNIT,
            'unit_price' => $unitPriceWithIgv,
            'affectation_igv_type_id' => self::AFFECTATION_TAXED,
            'total_base_igv' => $totalValue,
            'percentage_igv' => self::IGV_RATE * 100,
            'total_igv' => $totalIgv,
            'system_isc_type_id' => self::SYSTEM_ISC_NONE,
            'total_base_isc' => 0,
            'percentage_isc' => 0,
            'total_isc' => 0,
            'total_base_other_taxes' => 0,
            'percentage_other_taxes' => 0,
            'total_other_taxes' => 0,
            'total_plastic_bag_taxes' => 0,
            'total_taxes' => $totalIgv,
            'total_value' => $totalValue,
            'total_discount' => 0,
            'total_charge' => 0,
            'total' => $total,
            'attributes' => [],
            'charges' => [],
            'discounts' => [],
            'discount' => null,
            'attribute' => null,
            'name_product_pdf' => null,
            'name_product_xml' => null,
        ];
    }

    private function nextNumber(Series $series): int
    {
        $last = Document::query()
            ->where('series', $series->number)
            ->where('document_type_id', $series->document_type_id)
            ->max('number');

        return (int) ($last ?? 0) + 1;
    }

    private function establishmentSnapshot(int $establishmentId): array
    {
        $establishment = \App\Models\Tenant\Establishment::with(['province', 'department', 'district', 'country'])->find($establishmentId);
        if (!$establishment) {
            return [];
        }
        $data = $establishment->toArray();
        $data['urbanization'] = $establishment->urbanization ?? null;
        $data['email'] = $establishment->email ?? null;
        $data['telephone'] = $establishment->telephone ?? null;
        $data['address'] = $establishment->address ?? '-';
        return $data;
    }

    private function getOrCreateGenericConsumer(): Person
    {
        $person = Person::where('type', 'customers')
            ->where('number', '00000000')
            ->first();

        if (!$person) {
            $person = Person::create([
                'type' => 'customers',
                'identity_document_type_id' => '0',
                'number' => '00000000',
                'name' => 'CLIENTES VARIOS',
                'trade_name' => 'CLIENTES VARIOS',
                'country_id' => 'PE',
                'address' => '-',
                'enabled' => true,
            ]);
        }

        return $person;
    }

    private function customerSnapshot(?Person $customer): array
    {
        if (!$customer) {
            return [
                'identity_document_type_id' => '0',
                'identity_document_type_code' => '0',
                'identity_document_type' => ['id' => '0', 'description' => 'OTROS', 'code' => '0'],
                'number' => '00000000',
                'name' => 'CLIENTES VARIOS',
                'trade_name' => 'CLIENTES VARIOS',
                'address' => '-',
            ];
        }

        $customer->load(['identity_document_type', 'country', 'department', 'province', 'district']);
        $data = $customer->toArray();
        $data['identity_document_type_code'] = $customer->identity_document_type_id;
        $data['trade_name'] = $customer->trade_name ?: $customer->name;
        $data['address'] = $customer->address ?: '-';
        $data['department_id'] = $customer->department_id ?: '-';
        $data['province_id'] = $customer->province_id ?: '-';
        $data['district_id'] = $customer->district_id ?: '-';
        return $data;
    }
}
