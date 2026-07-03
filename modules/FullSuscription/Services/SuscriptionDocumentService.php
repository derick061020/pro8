<?php

namespace Modules\FullSuscription\Services;

use App\CoreFacturalo\Requests\Inputs\Common\ActionInput;
use App\CoreFacturalo\Requests\Inputs\Common\EstablishmentInput;
use App\CoreFacturalo\Requests\Inputs\Common\PersonInput;
use App\Http\Controllers\Tenant\DocumentController;
use App\Http\Requests\Tenant\DocumentRequest;
use App\Models\Tenant\Company;
use App\Models\Tenant\Item;
use App\Models\Tenant\Series;
use Illuminate\Support\Str;
use Modules\ApiPeruDev\Data\ServiceData;
use Modules\FullSuscription\Models\Tenant\UserRelSuscriptionPlan;

trait SuscriptionDocumentService
{
    public function document(UserRelSuscriptionPlan $suscription): array
    {
        $_data   = $this->transform($suscription->toArray(), $suscription->suscription_plan->items);
        $request = new DocumentRequest($_data);
        $response = app(DocumentController::class)->store($request);

        if (!isset($response['data'])) {
            return [
                'success' => false,
                'message' => $response['message'],
            ];
        }

        $_response = app(DocumentController::class)->send($response['data']['id']);

        return [
            'success' => $response['success'],
            'number'      => $response['data']['number_full'],
            'response'    => isset($_response['response']) ? $_response['response']['description'] : 'No se pudo enviar el documento',
            'document_id' => $response['data']['id'],
        ];
    }

    public function transform(array $values, $get_ids): array
    {
        $company              = Company::active();
        $soap_type_id         = $company->soap_type_id;
        $customer             = (object) $values['customer'];
        $identity_document_type_id = $customer->identity_document_type_id;
        $establishment_id     = auth()->user()->establishment_id;
        $document_type_id     = $this->getDocumentType($identity_document_type_id);
        $items                = $this->items($get_ids);

        $serie = Series::where([
            'establishment_id' => $establishment_id,
            'document_type_id' => $document_type_id,
        ])->first();

        $service = (new ServiceData())->exchange(now()->format('Y-m-d'));

        return [
            'actions' => ActionInput::set([
                'type'     => 'invoice',
                'group_id' => ($document_type_id === '01') ? '01' : '02',
            ]),
            'series'                  => $serie->number,
            'payment_condition_id'    => '01',
            'type'                    => 'invoice',
            'group_id'                => ($document_type_id === '01') ? '01' : '02',
            'establishment_id'        => $establishment_id,
            'establishment'           => EstablishmentInput::set($establishment_id),
            'user_id'                 => auth()->id(),
            'external_id'             => Str::uuid()->toString(),
            'document_type_id'        => $document_type_id,
            'number'                  => '#',
            'soap_type_id'            => $soap_type_id,
            'state_type_id'           => '01',
            'ubl_version'             => '2.1',
            'date_of_issue'           => now()->format('Y-m-d'),
            'time_of_issue'           => now()->format('H:i:s'),
            'customer_id'             => $customer->id,
            'customer'                => PersonInput::set($customer->id),
            'invoice'                 => [
                'operation_type_id' => '0101',
                'date_of_due'       => now()->format('Y-m-d'),
            ],
            'exchange_rate_sale'      => $service['sale'],
            'currency_type_id'        => 'PEN',
            'items'                   => $items,
            'charges'                 => $values['charges'] ?? [],
            'discounts'               => $values['discounts'] ?? [],
            'send_server'             => false,
            'is_editable'             => true,
            'total_discount'          => $values['total_discount'] ?? 0,
            'total_charge'            => $values['total_charge'] ?? 0,
            'total'                   => $values['total'],
            'total_taxed'             => $values['total_taxed'] ?? 0,
            'total_unaffected'        => $values['total_unaffected'] ?? 0,
            'total_exonerated'        => $values['total_exonerated'] ?? 0,
            'total_igv'               => $values['total_igv'] ?? 0,
            'total_isc'               => $values['total_isc'] ?? 0,
            'total_other_taxes'       => $values['total_other_taxes'] ?? 0,
            'total_base_other_taxes'  => $values['total_base_other_taxes'] ?? 0,
            'total_taxes'             => $values['total_taxes'] ?? 0,
            'total_prepayment'        => $values['total_prepayment'] ?? 0,
            'total_exportation'       => $values['total_exportation'] ?? 0,
            'total_free'              => $values['total_free'] ?? 0,
            'total_igv_free'          => $values['total_igv_free'] ?? 0,
            'total_base_isc'          => $values['total_base_isc'] ?? 0,
            'total_value'             => $values['total_value'] ?? 0,
            'subtotal'                => $values['total'],
            'payments'                => $this->payments($values['total']),
            'fee'                     => [],
            'hotel'                   => null,
            'transport'               => null,
            'legends'                 => [],
        ];
    }

    public function items($items): array
    {
        $_items = [];

        foreach ($items as $row) {
            $item = Item::find($row->item_id);

            $_items[] = [
                'item_id'                  => $item->id,
                'item'                     => [
                    'description'              => trim($item->description),
                    'item_type_id'             => $item->item_type_id,
                    'internal_id'              => $item->barcode === 'VARIOUS_ITEM' ? null : $item->internal_id,
                    'item_code'                => trim($item->item_code),
                    'item_code_gs1'            => $item->item_code_gs1,
                    'unit_type_id'             => optional($row->item)->unit_type_id ?? $item->unit_type_id,
                    'presentation'             => optional($row->item)->presentation ?? [],
                    'amount_plastic_bag_taxes' => $item->amount_plastic_bag_taxes,
                    'is_set'                   => $item->is_set,
                    'lots'                     => [],
                    'IdLoteSelected'           => false,
                    'model'                    => $item->model,
                    'sanitary'                 => $item->sanitary,
                    'cod_digemid'              => $item->cod_digemid,
                    'date_of_due'              => !empty($item->date_of_due) ? $item->date_of_due->format('Y-m-d') : null,
                    'has_igv'                  => optional($row->item)->has_igv ?? true,
                    'unit_price'               => $row->unit_price ?? 0,
                    'purchase_unit_price'      => $item->purchase_unit_price ?? 0,
                    'exchanged_for_points'     => optional($row->item)->exchanged_for_points ?? false,
                    'used_points_for_exchange' => optional($row->item)->used_points_for_exchange ?? null,
                ],
                'quantity'                 => $row->quantity,
                'unit_value'               => $row->unit_value,
                'price_type_id'            => $row->price_type_id,
                'unit_price'               => $row->unit_price,
                'affectation_igv_type_id'  => $row->affectation_igv_type_id,
                'total_base_igv'           => $row->total_base_igv,
                'percentage_igv'           => 18,
                'total_igv'                => $row->total_igv,
                'system_isc_type_id'       => $row->system_isc_type_id ?? null,
                'total_base_isc'           => $row->total_base_isc ?? null,
                'percentage_isc'           => $row->percentage_isc ?? null,
                'total_isc'                => $row->total_isc ?? null,
                'total_base_other_taxes'   => $row->total_base_other_taxes ?? null,
                'percentage_other_taxes'   => $row->percentage_other_taxes ?? null,
                'total_other_taxes'        => $row->total_other_taxes ?? null,
                'total_plastic_bag_taxes'  => 0,
                'total_taxes'              => $row->total_taxes,
                'total_value'              => $row->total_value,
                'total_charge'             => $row->total_charge ?? null,
                'total_discount'           => $row->total_discount ?? null,
                'total'                    => $row->total,
                'discounts'                => null,
                'charges'                  => null,
                'warehouse_id'             => $row->warehouse_id,
                'additional_information'   => null,
                'name_product_pdf'         => null,
                'name_product_xml'         => null,
                'update_description'       => null,
                'additional_data'          => null,
            ];
        }

        return $_items;
    }

    public function getDocumentType(string $identity_document_type): string
    {
        return match ($identity_document_type) {
            '6'     => '01',
            default => '03',
        };
    }

    public function payments(float $total): array
    {
        return [[
            'id'                      => null,
            'document_id'             => null,
            'date_of_payment'         => now()->format('Y-m-d'),
            'payment_method_type_id'  => '01',
            'payment'                 => $total,
            'payment_destination_id'  => 'cash',
        ]];
    }
}
