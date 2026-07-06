<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Precio personalizado para la web pública de reservas (landing). Cuando está
 * definido (> 0) sobreescribe el precio "desde" que se calcula a partir de las
 * tarifas asignadas a la habitación. Es opcional: si queda nulo, se mantiene el
 * comportamiento actual (mínimo de las tarifas).
 */
class AddWebPriceToHotelRoomsTable extends Migration
{
    public function up()
    {
        Schema::table('hotel_rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('hotel_rooms', 'web_price')) {
                $table->decimal('web_price', 12, 2)->nullable()->after('featured');
            }
        });
    }

    public function down()
    {
        Schema::table('hotel_rooms', function (Blueprint $table) {
            if (Schema::hasColumn('hotel_rooms', 'web_price')) {
                $table->dropColumn('web_price');
            }
        });
    }
}
