<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Equivalencia entre el id local del terminal y el id que el registro recibió
 * en el servidor online. Necesaria para reenviar relaciones (un pago que
 * apunta a un documento, un consumo que apunta a una habitación, etc.).
 */
class TenantCreateOfflineIdMapsTable extends Migration
{
    public function up()
    {
        Schema::create('offline_id_maps', function (Blueprint $table) {
            $table->bigIncrements('id');
            // En el terminal siempre vale '' (solo se mapea contra su servidor).
            // En el servidor identifica de qué terminal viene el id local, ya
            // que dos terminales distintos pueden traer el mismo id.
            $table->string('terminal_code', 20)->default('');
            $table->string('entity', 60);
            $table->unsignedBigInteger('local_id');
            $table->unsignedBigInteger('remote_id');
            $table->uuid('uuid')->nullable();
            $table->timestamps();

            $table->unique(['terminal_code', 'entity', 'local_id'], 'offline_id_maps_local_unique');
            $table->index(['entity', 'remote_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('offline_id_maps');
    }
}
