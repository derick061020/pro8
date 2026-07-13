<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Periodos de mantenimiento programado de una habitación.
 *
 * Se crean desde el calendario de reservas: al seleccionar uno o varios días
 * se puede optar por "poner en mantenimiento" en lugar de reservar. Cuando la
 * fecha de inicio llega, la habitación pasa a estado MANTENIMIENTO; al terminar
 * el rango vuelve a DISPONIBLE (ver HotelRoomMaintenance::reconcile()).
 */
class CreateHotelRoomMaintenancesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hotel_room_maintenances')) {
            return;
        }

        Schema::create('hotel_room_maintenances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('hotel_room_id');
            $table->unsignedInteger('establishment_id')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason', 250)->nullable();
            // SCHEDULED (programado) | ACTIVE (en curso) | DONE (finalizado)
            $table->string('status', 20)->default('SCHEDULED');
            $table->timestamps();

            $table->index('hotel_room_id');
            $table->index('establishment_id');
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('hotel_room_maintenances');
    }
}
