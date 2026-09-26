<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hilo de observaciones de la habitación (estilo "chatter" de Odoo): cada
     * nota es un mensaje con su autor y su fecha, en vez del campo único
     * `hotel_rents.notes` que se sobrescribía en cada edición.
     *
     * `hotel_rents.notes` se conserva y se mantiene sincronizado con la última
     * nota, porque de él dependen el indicador y el tooltip de la tarjeta de
     * recepción, el reporte de reservas y el calendario.
     */
    public function up()
    {
        // Mismo guard que hotel_rent_changes: en varios tenants las tablas se
        // crean fuera del runner, y sin esto el runner muere con "table already
        // exists" y bloquea todas las migraciones posteriores por fecha.
        if (Schema::hasTable('hotel_rent_notes')) {
            return;
        }

        Schema::create('hotel_rent_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('hotel_rent_id');
            $table->unsignedInteger('user_id')->nullable();
            // Nombre del autor congelado: si el usuario se borra, el mensaje
            // sigue diciendo quién lo escribió.
            $table->string('user_name', 100)->nullable();
            $table->text('body');
            $table->timestamps();

            $table->foreign('hotel_rent_id')->references('id')->on('hotel_rents')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->index(['hotel_rent_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('hotel_rent_notes');
    }
};
