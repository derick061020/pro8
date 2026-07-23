<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropOpenaiColumnsFromConfigurations extends Migration
{
    // El proveedor de IA se centralizo en system_configurations (el reseller lo
    // configura una sola vez y lo comparte con todos los tenants). Quitamos las
    // columnas openai_* del tenant porque ya no se usan.
    public function up()
    {
        // No todos los tenants tienen estas columnas (algunos fueron creados
        // antes de que existieran). Solo dropeamos lo que existe.
        Schema::table('configurations', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('configurations', 'openai_api_key')) {
                $cols[] = 'openai_api_key';
            }
            if (Schema::hasColumn('configurations', 'openai_model')) {
                $cols[] = 'openai_model';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('configurations', 'openai_api_key')) {
                $table->string('openai_api_key', 255)->nullable();
            }
            if (!Schema::hasColumn('configurations', 'openai_model')) {
                $table->string('openai_model', 100)->nullable();
            }
        });
    }
}
