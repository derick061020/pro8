<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddPublicidadToConfigurationEcommerce extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configuration_ecommerce', function (Blueprint $table) {
            $table->boolean('publicidad_activa')->default(false)->after('preferences');
            $table->string('publicidad_texto')->nullable()->after('publicidad_activa');
            $table->string('publicidad_color_fondo')->default('#000000')->after('publicidad_texto');
            $table->string('publicidad_link')->nullable()->after('publicidad_color_fondo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configuration_ecommerce', function (Blueprint $table) {
            $table->dropColumn(['publicidad_activa', 'publicidad_texto', 'publicidad_color_fondo', 'publicidad_link']);
        });
    }
}
