<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Tenant\ItemUnitType;
use App\Models\Tenant\ItemUnitTypePrice;
use App\Models\Tenant\Configuration;

class MigrateItemUnitTypePricesToNewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Obtener labels desde configuración global, con fallback a valores por defecto
        $configuration = Configuration::first();

        $price1Label = $configuration->price1_label ?? 'Precio 1';
        $price2Label = $configuration->price2_label ?? 'Precio 2';
        $price3Label = $configuration->price3_label ?? 'Precio 3';

        // Migrar datos existentes de item_unit_types
        ItemUnitType::chunk(500, function ($itemUnitTypes) use ($price1Label, $price2Label, $price3Label) {
            foreach ($itemUnitTypes as $itemUnitType) {
                // Crear los 3 precios basados en los datos actuales
                $prices = [
                    [
                        'item_unit_type_id' => $itemUnitType->id,
                        'position' => 1,
                        'price' => $itemUnitType->price1 ?? 0,
                        'label' => $price1Label,
                        'is_active' => true,
                    ],
                    [
                        'item_unit_type_id' => $itemUnitType->id,
                        'position' => 2,
                        'price' => $itemUnitType->price2 ?? 0,
                        'label' => $price2Label,
                        'is_active' => true,
                    ],
                    [
                        'item_unit_type_id' => $itemUnitType->id,
                        'position' => 3,
                        'price' => $itemUnitType->price3 ?? 0,
                        'label' => $price3Label,
                        'is_active' => true,
                    ],
                ];

                ItemUnitTypePrice::insert($prices);
            }
        });

        // Hacer los campos price1, price2, price3 nullables (deprecados pero mantenidos)
        Schema::table('item_unit_types', function (Blueprint $table) {
            $table->decimal('price1', 12, 2)->nullable()->change();
            $table->decimal('price2', 12, 2)->nullable()->change();
            $table->decimal('price3', 12, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar todos los registros de la tabla de precios
        ItemUnitTypePrice::truncate();

        // Revertir los campos a not null (si es necesario)
        Schema::table('item_unit_types', function (Blueprint $table) {
            $table->decimal('price1', 12, 2)->default(0)->change();
            $table->decimal('price2', 12, 2)->default(0)->change();
            $table->decimal('price3', 12, 2)->default(0)->change();
        });
    }
}
