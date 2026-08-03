<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bloques de correlativos reservados para los terminales offline.
 *
 * En el servidor online la tabla registra qué bloque se le entregó a cada
 * terminal, para no volver a entregarlo ni emitir esos números por su cuenta.
 * En el terminal registra el bloque propio y hasta dónde se consumió.
 */
class TenantCreateOfflineNumberRangesTable extends Migration
{
    public function up()
    {
        Schema::create('offline_number_ranges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();

            $table->string('terminal_code', 20);
            $table->string('model_alias', 60)->default('document'); // document|sale_note|dispatch...
            $table->string('document_type_id', 3)->nullable();
            $table->string('series', 10);

            $table->unsignedBigInteger('from_number');
            $table->unsignedBigInteger('to_number');
            // Último número efectivamente consumido. NULL = aún no se usó ninguno.
            $table->unsignedBigInteger('current_number')->nullable();

            // active|exhausted|released
            $table->string('status', 15)->default('active');

            $table->timestamp('allocated_at')->nullable();
            $table->timestamp('exhausted_at')->nullable();
            // Última vez que el terminal informó su avance al servidor
            $table->timestamp('reported_at')->nullable();

            $table->timestamps();

            $table->index(['terminal_code', 'status']);
            $table->index(['model_alias', 'document_type_id', 'series', 'status'], 'offline_ranges_series_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('offline_number_ranges');
    }
}
