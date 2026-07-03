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
        unset($rows[0]); // quitar encabezado

        $records = $rows;
        $enable_list_product = Configuration::first()->enable_list_product;

        if (!$enable_list_product) {
            $records = $rows->unique(fn ($row) => $row[0]);
        }

        $activeLabels = PriceLabel::active()->ordered()->get();

        foreach ($records as $row) {
            $internal_id = ($row[0]) ?: null;
            if (!$internal_id) continue;

            $item = Item::where('internal_id', $internal_id)->first();
            if (!$item) continue;

            if ($enable_list_product) {
                $unit_type_id = $row[1];
                $factor       = $row[2];
                $description  = $row[3];
                $prices       = $row->slice(4)->values();

                // buscar la presentación existente; si no existe, crearla
                $itemUnitType = $item->item_unit_types()
                    ->where('description', $description)
                    ->where('unit_type_id', $unit_type_id)
                    ->first();

                if (!$itemUnitType) {
                    $itemUnitType = $item->item_unit_types()->create([
                        'description'   => $description,
                        'unit_type_id'  => $unit_type_id,
                        'quantity_unit' => $factor,
                        'price1' => 0, 'price2' => 0, 'price3' => 0,
                    ]);
                }
            } else {
                // modo simple: [codigo, precios...] → la presentación por defecto del producto
                $prices = $row->slice(1)->values();

                $itemUnitType = $item->item_unit_types()->first();

                if (!$itemUnitType) {
                    $itemUnitType = $item->item_unit_types()->create([
                        'description'   => $item->unit_type->description,
                        'unit_type_id'  => $item->unit_type_id,
                        'quantity_unit' => 1,
                        'price1' => 0, 'price2' => 0, 'price3' => 0,
                    ]);
                }
            }

            // ACTUALIZAR (o crear) el precio de cada label activo
            foreach ($prices as $index => $price) {
                if (!isset($activeLabels[$index])) continue;
                $label = $activeLabels[$index];

                ItemUnitTypePrice::updateOrCreate(
                    [
                        'item_unit_type_id' => $itemUnitType->id,
                        'price_label_id'    => $label->id,
                    ],
                    [
                        'price' => is_numeric($price) ? $price : 0,
                    ]
                );
            }

            $registered++;
        }

        $this->data = compact('total', 'registered');
    }

    public function getData()
    {
        return $this->data;
    }
}
