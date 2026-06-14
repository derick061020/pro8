<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRucApiProviderToConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            // Proveedor de consulta de RUC/DNI a usar por el tenant.
            // 'apiperu' => API que ya esta en el sistema (apiperu.dev / token)
            // 'sunat'   => Consulta directa a SUNAT (ww1.sunat.gob.pe)
            if (!Schema::hasColumn('configurations', 'ruc_api_provider')) {
                $table->string('ruc_api_provider', 20)->default('apiperu')->after('token_apiruc');
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
            if (Schema::hasColumn('configurations', 'ruc_api_provider')) {
                $table->dropColumn('ruc_api_provider');
            }
        });
    }
}
