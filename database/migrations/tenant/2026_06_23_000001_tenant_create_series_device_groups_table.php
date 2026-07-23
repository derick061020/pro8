<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Grupos de series dedicadas vinculables a un equipo/dispositivo.
 *
 * El vínculo se identifica por el NOMBRE del equipo (bound_device_name), que el cliente
 * persiste en localStorage. Mientras un grupo tenga bound_device_name, queda bloqueado
 * para el resto de equipos (se muestra "en uso por {nombre}").
 *
 * Ver docs/series-grupos-dedicados-plan.md (§4.1, §9-A/D).
 */
class TenantCreateSeriesDeviceGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('series_device_groups')) {
            Schema::create('series_device_groups', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('establishment_id')->comment('El grupo vive en un establecimiento');
                $table->string('name')->comment('Nombre del grupo: ej. Caja 1');
                $table->string('module_value')->nullable()->comment('Modulo asociado (uno solo). Catalogo extensible');
                $table->string('bound_device_name')->nullable()->comment('Nombre del equipo vinculado (clave de vinculo)');
                $table->unsignedInteger('bound_user_id')->nullable()->comment('Usuario que vinculo el grupo');
                $table->timestamp('bound_at')->nullable()->comment('Fecha de vinculo al equipo');
                $table->timestamps();

                $table->foreign('establishment_id')->references('id')->on('establishments');
                $table->index('bound_device_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('series_device_groups');
    }
}
