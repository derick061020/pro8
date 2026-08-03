<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bandeja de salida (outbox). Cada cambio local que debe viajar al servidor
 * online se registra aquí y se envía cuando hay conexión.
 */
class TenantCreateOfflineSyncQueueTable extends Migration
{
    public function up()
    {
        Schema::create('offline_sync_queue', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Identificador único e inmutable del cambio. El servidor lo usa
            // para descartar reenvíos sin duplicar registros.
            $table->uuid('uuid')->unique();
            $table->string('terminal_code', 20)->nullable();

            // Alias de la entidad según Modules\Offline\Services\EntityRegistry
            $table->string('entity', 60);
            $table->unsignedBigInteger('entity_id');
            $table->string('operation', 10)->default('create'); // create|update|delete

            $table->longText('payload')->nullable();
            // uuids de otros elementos de la cola que deben viajar antes que este
            $table->text('depends_on')->nullable();
            $table->unsignedSmallInteger('priority')->default(100);

            // pending|sending|synced|failed|conflict
            $table->string('status', 15)->default('pending');
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('next_attempt_at')->nullable();

            $table->unsignedBigInteger('remote_id')->nullable();
            $table->timestamp('synced_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'priority', 'id'], 'offline_sync_queue_dispatch_index');
            $table->index(['entity', 'entity_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('offline_sync_queue');
    }
}
