<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Visibilidad de la habitación en la web pública de reservas (landing).
 *
 * Permite OCULTAR una habitación solo en la web sin afectar su uso interno en
 * el sistema (recepción, calendario, alquileres siguen funcionando normal). Es
 * independiente de `active` (que oculta la habitación en todo el sistema).
 *
 * Por defecto true → todas las habitaciones existentes siguen visibles en web.
 */
class AddWebVisibleToHotelRoomsTable extends Migration
{
    public function up()
    {
        Schema::table('hotel_rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('hotel_rooms', 'web_visible')) {
                $table->boolean('web_visible')->default(true);
            }
        });
    }

    public function down()
    {
        Schema::table('hotel_rooms', function (Blueprint $table) {
            if (Schema::hasColumn('hotel_rooms', 'web_visible')) {
                $table->dropColumn('web_visible');
            }
        });
    }
}
