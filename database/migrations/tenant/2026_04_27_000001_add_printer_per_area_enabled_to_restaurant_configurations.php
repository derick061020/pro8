<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega el campo printer_per_area_enabled a restaurant_configurations.
     *
     * Cuando está activo, permite asignar una impresora distinta a cada
     * área de preparación. Cuando está inactivo, todas las áreas usan la
     * impresora seleccionada en "Impresora - Comanda".
     */
    public function up(): void
    {
        Schema::table('restaurant_configurations', function (Blueprint $table) {
            $table->boolean('printer_per_area_enabled')->default(false)->after('printer_areas_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_configurations', function (Blueprint $table) {
            $table->dropColumn('printer_per_area_enabled');
        });
    }
};
