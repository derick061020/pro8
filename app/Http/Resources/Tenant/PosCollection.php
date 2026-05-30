<?php

namespace App\Http\Resources\Tenant;

use App\Models\Tenant\Configuration;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant\Catalogs\CurrencyType;
use App\Models\Tenant\PriceLabel;

class PosCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($row, $key) {

            $configuration = Configuration::first();
            $sale_unit_price = $this->getSaleUnitPrice($row, $configuration);

            $currency = $row->currency_type;
            if(empty($currency )){
                $currency = CurrencyType::first();
            }

            $defaultImage = $configuration->product_default_image ?? 'imagen-no-disponible.jpg';
            $defaultImagePath = $defaultImage === 'imagen-no-disponible.jpg'
                ? asset('logo/imagen-no-disponible.jpg')
                : asset('storage/defaults/' . $defaultImage); 
            
            $allPricesLabel = PriceLabel::all();


            return [
                'stock' => $row->getStockByWarehouse(),
                'id' => $row->id,
                'item_id' => $row->id,
                'full_description' => ($row->internal_id) ? $row->internal_id . ' - ' . $row->description : $row->description,
                'name' => $row->name,
                'second_name' => $row->second_name,
                'description' => ($row->brand->name) ? $row->description.' - '.$row->brand->name : $row->description,
                'currency_type_id' => $row->currency_type_id,
                'internal_id' => $row->internal_id,
                'currency_type_symbol' => $currency->symbol,
                'sale_unit_price' => $sale_unit_price,
                'purchase_unit_price' => $row->purchase_unit_price,
                'unit_type_id' => $row->unit_type_id,
                'aux_unit_type_id' => $row->unit_type_id,
                'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                'calculate_quantity' => (bool) $row->calculate_quantity,
                'has_igv' => (bool) $row->has_igv,
                'is_set' => (bool) $row->is_set,
                'active' => $row->active,
                'edit_unit_price' => false,
                'aux_quantity' => 1,
                'edit_sale_unit_price' => $sale_unit_price,
                'aux_sale_unit_price' => $sale_unit_price,
                'image_url' => ($row->image && $row->image !== 'imagen-no-disponible.jpg') 
                    ? asset('storage/uploads/items/' . $row->image)
                    : $defaultImagePath,
                'warehouses' => collect($row->warehouses)->transform(function ($row) {
                    return [
                        'warehouse_description' => $row->warehouse->description,
                        'stock' => $row->stock,
                    ];
                }),
                'category_id' => ($row->category) ? $row->category->id : null,
                'sets' => collect($row->sets)->transform(function ($r) {
                    return [
                        $r->individual_item->description,
                    ];
                }),
                'item_unit_types' => collect($row->item_unit_types)->transform(function($row) use($configuration, $allPricesLabel){
                    $row->load('prices');
                    $labels_id = $row->prices->pluck('price_label_id')->toArray();
                    $prices = $row->prices->map(function($price)  use($row) {
                            $price_label = $price->priceLabel;
                                return [
                                    'id'             => $price->id,
                                    'price_label_id' => $price->price_label_id,
                                    'position'       => $price_label->position,
                                    'description'    => $row->description,  
                                    'unit_type_id'   => $row->unit_type_id,
                                    'label'          => $price_label->label,
                                    'price'          => $price ? number_format($price->price, 2, '.', '') : 0,
                                    'is_active'      => $price ? (bool) $price->is_active : false,
                                ];
                            });
                    
                    
                    $missingLabel = $allPricesLabel->whereNotIn('id', $labels_id)->first();

                    if ($missingLabel) {
                        $prices->push([
                            'id' => null,
                            'price_label_id' => $missingLabel->id,
                            'position' => $missingLabel->position,
                            'label' => $missingLabel->label,
                            'price' => 0,
                            'is_active' => $missingLabel->is_active
                        ]);
                    }
                    return [
                        'id' => $row->id,
                        'description' => "{$row->description}",
                        'item_id' => $row->item_id,
                        'unit_type_id' => $row->unit_type_id,
                        'quantity_unit' => number_format($row->quantity_unit, $configuration->decimal_quantity, ".",""),
                        'price_default' => $row->price_default,
                        'barcode' => $row->barcode ?? '',
                        'prices' => $prices->toArray(),
                    ];
                }),
                'unit_type' => $row->item_unit_types,
                'category' => ($row->category) ? $row->category->name : null,
                'brand' => ($row->brand) ? $row->brand->name : null,
                'has_plastic_bag_taxes' => (bool) $row->has_plastic_bag_taxes,
                'amount_plastic_bag_taxes' => $row->amount_plastic_bag_taxes,

                'has_plastic_bag_taxes' => (bool) $row->has_plastic_bag_taxes,

                'has_isc' => (bool)$row->has_isc,
                'system_isc_type_id' => $row->system_isc_type_id,
                'percentage_isc' => $row->percentage_isc,
                
                'exchange_points' => $row->exchange_points,
                'quantity_of_points' => $row->quantity_of_points,
                'exchanged_for_points' => false, //para determinar si desea canjear el producto
                'used_points_for_exchange' => null, //total de puntos
                'original_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                'restrict_sale_cpe' => $row->restrict_sale_cpe,
            ];
        });
    }

    
    private function getSaleUnitPrice($row, $configuration){

        $sale_unit_price = number_format($row->sale_unit_price, $configuration->decimal_quantity, ".", "");
        
        if($configuration->active_warehouse_prices){

            $warehouse_price = $row->warehousePrices()->where('warehouse_id', auth()->user()->establishment->warehouse->id)->first();

            if($warehouse_price){

                $sale_unit_price = number_format($warehouse_price->price, $configuration->decimal_quantity, ".", "");

            }else{

                if($row->warehousePrices()->count() > 0){
                    $sale_unit_price = number_format($row->warehousePrices()->first()->price, $configuration->decimal_quantity, ".", "");
                }

            }

        }

        return $sale_unit_price;
    }
    
}
