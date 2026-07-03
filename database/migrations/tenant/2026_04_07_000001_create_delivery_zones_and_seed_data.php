<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Crea las tablas de zonas de delivery, agrega el campo de mensaje
     * en la configuración del ecommerce e inserta el ítem de costo de envío.
     */
    public function up()
    {
        // 1. Tabla principal de zonas de delivery
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->decimal('price', 12, 2)->default(0);
            // Si true, la zona aplica sólo a las ubicaciones exactas especificadas
            $table->boolean('specify_zone')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // 2. Tabla pivot para las ubicaciones que cubre cada zona
        Schema::create('delivery_zone_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_zone_id');
            $table->char('department_id', 2);
            $table->char('province_id', 4)->nullable();
            $table->char('district_id', 6)->nullable();
            $table->timestamps();

            $table->foreign('delivery_zone_id')
                  ->references('id')
                  ->on('delivery_zones')
                  ->onDelete('cascade');

            // Índice compuesto para acelerar el matching en checkout
            $table->index(['department_id', 'province_id', 'district_id'], 'idx_delivery_zone_ubigeo');
        });

        // 3. Campo de mensaje personalizado cuando no hay cobertura
        Schema::table('configuration_ecommerce', function (Blueprint $table) {
            $table->text('delivery_no_coverage_message')->nullable()->after('about_us');
        });

        // 4. Ítem de servicio "Costo de Envío" — se inserta si no existe previamente
        DB::table('items')->insertOrIgnore([
            'description'                       => 'Costo de Envío',
            'internal_id'                       => 'DELIVERY-ECOM',
            'item_type_id'                      => '02',
            'unit_type_id'                      => 'ZZ',
            'currency_type_id'                  => 'PEN',
            'sale_unit_price'                   => 0.00,
            'purchase_unit_price'               => 0.00,
            'sale_affectation_igv_type_id'      => '10',
            'purchase_affectation_igv_type_id'  => '10',
            'stock'                             => 0,
            'stock_min'                         => 0,
            'has_isc'                           => 0,
            'has_igv'                           => 1,
            'purchase_has_igv'                  => 1,
            'active'                            => 1,
            'is_set'                            => 0,
            'apply_store'                       => 0,
            'created_at'                        => now(),
            'updated_at'                        => now(),
        ]);
    }

    /**
     * Revierte los cambios de esta migración en orden inverso.
     */
    public function down()
    {
        // Eliminar el ítem de costo de envío
        DB::table('items')->where('internal_id', 'DELIVERY-ECOM')->delete();

        // Quitar la columna de mensaje de configuración
        Schema::table('configuration_ecommerce', function (Blueprint $table) {
            $table->dropColumn('delivery_no_coverage_message');
        });

        // Eliminar tablas (el cascade maneja delivery_zone_locations primero)
        Schema::dropIfExists('delivery_zone_locations');
        Schema::dropIfExists('delivery_zones');
    }
};
