<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bitácora de sincronización. Sirve para diagnosticar en sitio por qué un
 * terminal no está subiendo sus ventas sin tener que leer los logs de Laravel.
 */
class TenantCreateOfflineSyncLogsTable extends Migration
{
    public function up()
    {
        Schema::create('offline_sync_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('terminal_code', 20)->nullable();
            $table->string('direction', 12); // push|pull|ping|ranges|update
            $table->string('entity', 60)->nullable();
            $table->boolean('success')->default(true);
            $table->unsignedInteger('records')->default(0);
            $table->text('message')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamps();

            $table->index(['direction', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('offline_sync_logs');
    }
}
