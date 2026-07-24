<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotel_rents', function (Blueprint $table) {
            if (!Schema::hasColumn('hotel_rents', 'reservation_origin')) {
                $table->string('reservation_origin', 30)->nullable()->after('travel_reason')
                    ->comment('Medio por el que se realizó la reserva: whatsapp, correo, celular, presencial');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotel_rents', function (Blueprint $table) {
            if (Schema::hasColumn('hotel_rents', 'reservation_origin')) {
                $table->dropColumn('reservation_origin');
            }
        });
    }
};
