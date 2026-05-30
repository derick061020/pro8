<?php

namespace Modules\Item\Imports;

use App\Models\Tenant\Catalogs\UnitType;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Item;
use App\Models\Tenant\Warehouse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Item\Models\Category;
use Modules\Item\Models\Brand;
use App\Models\Tenant\ItemUnitType;
use App\Models\Tenant\ItemUnitTypePrice;
use App\Models\Tenant\PriceLabel;

class ItemListPriceImport implements ToCollection
{
    use Importable;

    protected $data;

    public function collection(Collection $rows)
    {
            $total = count($rows);
            $registered = 0;
            unset($rows[0]);

            $records = $rows;
            $enable_list_product = Configuration::first()->enable_list_product;

            if (!$enable_list_product) {
                $records= $rows->unique(fn ($row) => $row[0]);
            }


            foreach ($records as $row)
            {
                $factor = 1;
                $internal_id = null;
                $unit_type_id = null;
                $description = null;

                $internal_id = ($row[0])?:null; // Codigo interno
                if ($enable_list_product) {
                    $prices = $row->slice(4)->values();
                } else {
                    $prices = $row->slice(1)->values();
                }
                $item = null;

                if($internal_id) {
                    $item = Item::where('internal_id', $internal_id)
                                    ->first();
                }
                
                if($item) {
                    $item_unit_type = ItemUnitType::where('item_id', $item->id)
                                                    ->first();
                    if(!$item_unit_type || $enable_list_product){

                        if (!$enable_list_product) {
                            $description = $item->unit_type->description;
                            $unit_type_id = $item->unit_type_id; // Unidad 

                        } else {
                            $unit_type_id = $row[1]; // Unidad 
                            $factor = $row[2]; // Factor
                            $description = $row[3]; // Descripción
                        }

                        $itemUnitType = $item->item_unit_types()->create([
                            'description' => $description,
                            'unit_type_id' => $unit_type_id,
                            'quantity_unit' => $factor,
                            'price1' => 0,
                            'price2' => 0,
                            'price3' => 0,
                        ]);

                        foreach ($prices as $index => $price) {
                            $priceLabel = PriceLabel::where('position', ($index + 1));
                            ItemUnitTypePrice::create([
                                'item_unit_type_id' => $itemUnitType->id,
                                'price' => is_numeric($price) ? $price : 0,
                                'price_label_id' => $priceLabel->first()->id
                            ]);
                        }
                    }

                    $registered += 1;
                } 
            }

            $this->data = compact('total', 'registered');

    }

    public function getData()
    {
        return $this->data;
    }
}
