<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega el campo printer_areas_enabled a restaurant_configurations.
     *
     * Cuando está activo, indica que se usarán las áreas de preparación para
     * enrutar las comandas, en lugar de la impresora de comanda general.
     * Al activarse, la impresora de comanda queda inhabilitada.
     */
    public function up(): void
    {
        Schema::table('restaurant_configurations', function (Blueprint $table) {
            $table->boolean('printer_areas_enabled')->default(false)->after('printer_name_precuenta');
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_configurations', function (Blueprint $table) {
            $table->dropColumn('printer_areas_enabled');
        });
    }
};
