<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Checklist de la limpieza: la misma lista de artículos en dos secciones
     * (lo que se puso y lo que se cambió). Recepción marca al asignar qué hay
     * que hacer y la encargada marca lo hecho; cada casilla guarda la hora en
     * que se completó y quién la marcó.
     */
    public function up()
    {
        // Mismo guard que el resto de tablas del módulo: en varios tenants se
        // crean fuera del runner y sin esto bloquean las migraciones siguientes.
        if (Schema::hasTable('hotel_cleaning_checklist_items')) {
            return;
        }

        Schema::create('hotel_cleaning_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_cleaning_id');
            $table->string('section', 20);    // placed | changed
            $table->string('item_key', 40);   // towels, toilet_paper, soap, sheets, duvets, pillows
            $table->boolean('planned')->default(true);
            $table->boolean('done')->default(false);
            $table->dateTime('done_at')->nullable();
            $table->unsignedInteger('done_by')->nullable();
            $table->timestamps();

            $table->foreign('hotel_cleaning_id')
                  ->references('id')->on('hotel_cleanings')->onDelete('cascade');
            $table->foreign('done_by')
                  ->references('id')->on('users')->onDelete('set null');

            // Una sola fila por artículo y sección dentro de cada limpieza.
            $table->unique(['hotel_cleaning_id', 'section', 'item_key'], 'cleaning_checklist_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hotel_cleaning_checklist_items');
    }
};
