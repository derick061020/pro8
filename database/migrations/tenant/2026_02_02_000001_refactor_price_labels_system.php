<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\ItemUnitType;
use App\Models\Tenant\ItemUnitTypePrice;
use Illuminate\Support\Facades\DB;

class RefactorPriceLabelsSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Crear tabla price_labels
        Schema::create('price_labels', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('position')->unique()->comment('Orden de visualización del precio');
            $table->string('label', 50)->comment('Etiqueta personalizada del precio (ej: Precio Mayorista)');
            $table->boolean('is_active')->default(true)->comment('Si está activo para usar en ventas');
            $table->timestamps();

            $table->index(['is_active', 'position']);
        });

        // 2. Insertar 3 labels desde Configuration
        $config = Configuration::first();

        DB::table('price_labels')->insert([
            [
                'id' => 1,
                'position' => 1,
                'label' => $config->price1_label ?? 'Precio 1',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'position' => 2,
                'label' => $config->price2_label ?? 'Precio 2',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'position' => 3,
                'label' => $config->price3_label ?? 'Precio 3',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 3. LIMPIAR tabla item_unit_type_prices (eliminar datos de migración anterior)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        ItemUnitTypePrice::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 4. Modificar estructura: agregar price_label_id, eliminar label y position
        Schema::table('item_unit_type_prices', function (Blueprint $table) {
            // Agregar nueva columna FK
            $table->unsignedInteger('price_label_id')->after('item_unit_type_id');

            // Eliminar columnas obsoletas
            $table->dropColumn(['label', 'position']);
        });

        // 5. Re-poblar desde datos legacy (price1, price2, price3 de item_unit_types)
        ItemUnitType::chunk(500, function ($itemUnitTypes) {
            $inserts = [];

            foreach ($itemUnitTypes as $itemUnitType) {
                // Crear 3 registros por cada ItemUnitType (FK a price_labels 1, 2, 3)
                $inserts[] = [
                    'item_unit_type_id' => $itemUnitType->id,
                    'price_label_id' => 1,
                    'price' => $itemUnitType->price1 ?? 0,
                    'is_active' => true,
                ];
                $inserts[] = [
                    'item_unit_type_id' => $itemUnitType->id,
                    'price_label_id' => 2,
                    'price' => $itemUnitType->price2 ?? 0,
                    'is_active' => true,
                ];
                $inserts[] = [
                    'item_unit_type_id' => $itemUnitType->id,
                    'price_label_id' => 3,
                    'price' => $itemUnitType->price3 ?? 0,
                    'is_active' => true,
                ];
            }

            // Insert en batch para mejor performance
            foreach (array_chunk($inserts, 1000) as $chunk) {
                ItemUnitTypePrice::insert($chunk);
            }
        });

        // 6. Agregar constraints (después de poblar datos)
        Schema::table('item_unit_type_prices', function (Blueprint $table) {
            // Foreign key con RESTRICT (no permitir borrar label si está en uso)
            $table->foreign('price_label_id')
                  ->references('id')
                  ->on('price_labels')
                  ->onDelete('restrict');

            // Unique constraint: un ItemUnitType no puede tener dos precios con el mismo label
            $table->unique(['item_unit_type_id', 'price_label_id'], 'unique_item_unit_price_label');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revertir constraints
        Schema::table('item_unit_type_prices', function (Blueprint $table) {
            $table->dropForeign(['price_label_id']);
            $table->dropUnique('unique_item_unit_price_label');
        });

        // Limpiar datos
        ItemUnitTypePrice::truncate();

        // Restaurar columnas antiguas
        Schema::table('item_unit_type_prices', function (Blueprint $table) {
            $table->dropColumn('price_label_id');
            $table->tinyInteger('position')->after('item_unit_type_id');
            $table->string('label', 50)->after('position');
        });

        // Re-poblar con estructura antigua (desde Configuration)
        $config = Configuration::first();
        $labels = [
            1 => $config->price1_label ?? 'Precio 1',
            2 => $config->price2_label ?? 'Precio 2',
            3 => $config->price3_label ?? 'Precio 3',
        ];

        ItemUnitType::chunk(500, function ($itemUnitTypes) use ($labels) {
            $inserts = [];
            foreach ($itemUnitTypes as $itemUnitType) {
                $inserts[] = [
                    'item_unit_type_id' => $itemUnitType->id,
                    'position' => 1,
                    'label' => $labels[1],
                    'price' => $itemUnitType->price1 ?? 0,
                    'is_active' => true,
                ];
                $inserts[] = [
                    'item_unit_type_id' => $itemUnitType->id,
                    'position' => 2,
                    'label' => $labels[2],
                    'price' => $itemUnitType->price2 ?? 0,
                    'is_active' => true,
                ];
                $inserts[] = [
                    'item_unit_type_id' => $itemUnitType->id,
                    'position' => 3,
                    'label' => $labels[3],
                    'price' => $itemUnitType->price3 ?? 0,
                    'is_active' => true,
                ];
            }
            foreach (array_chunk($inserts, 1000) as $chunk) {
                ItemUnitTypePrice::insert($chunk);
            }
        });

        // Eliminar tabla price_labels
        Schema::dropIfExists('price_labels');
    }
}
