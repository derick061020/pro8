<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApisuToConfigurationsTable extends Migration
{
    /**
     * URL y token de la API SUNAT propia (apisu), editables desde
     * Sistema → Configuración → Consulta RUC/DNIe, igual que los de apiperu.
     * Vacíos = se usan APISU_URL / APISU_KEY del .env.
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('configurations', 'apisu_url')) {
                $table->string('apisu_url')->nullable()->after('token_apiruc');
            }
            if (!Schema::hasColumn('configurations', 'apisu_key')) {
                $table->text('apisu_key')->nullable()->after('apisu_url');
            }
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            foreach (['apisu_key', 'apisu_url'] as $column) {
                if (Schema::hasColumn('configurations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
