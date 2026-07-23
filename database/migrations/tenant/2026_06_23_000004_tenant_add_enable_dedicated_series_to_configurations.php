<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Toggle por tenant para habilitar el modo "Dedicado" en la vista de series
 * (muestra la opcion/tab Dedicado en el header). Por defecto desactivado.
 *
 * Ver docs/series-grupos-dedicados-plan.md (§4.6).
 */
class TenantAddEnableDedicatedSeriesToConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            if (! Schema::hasColumn('configurations', 'enable_dedicated_series')) {
                $table->boolean('enable_dedicated_series')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            if (Schema::hasColumn('configurations', 'enable_dedicated_series')) {
                $table->dropColumn('enable_dedicated_series');
            }
        });
    }
}
