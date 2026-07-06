<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Configuración personalizable de la web pública de reservas (landing) por
 * establecimiento: slider, galería, parallax, testimonios, sección "sobre el
 * hotel", ventajas, textos de secciones, colores, etc.
 *
 * Todo se guarda en una única columna JSON `data` para poder ampliar la
 * personalización sin nuevas migraciones. Una fila por establecimiento.
 */
class CreateHotelLandingSettingsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hotel_landing_settings')) {
            Schema::create('hotel_landing_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('establishment_id')->nullable()->index();
                $table->longText('data')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('hotel_landing_settings');
    }
}
