<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Permite elegir un cliente (tenant) como "web principal": su web de reservas
 * pasa a ser la portada del dominio central del sistema.
 */
class AddMainWebClientIdToConfigurations extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('configurations', 'main_web_client_id')) {
                $table->unsignedInteger('main_web_client_id')->nullable()->after('locked_admin');
            }
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            if (Schema::hasColumn('configurations', 'main_web_client_id')) {
                $table->dropColumn('main_web_client_id');
            }
        });
    }
}
