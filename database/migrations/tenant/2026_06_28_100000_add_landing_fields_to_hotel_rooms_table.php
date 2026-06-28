<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Campos para enriquecer las habitaciones de cara a la web pública de reservas
 * (landing): galería de imágenes, amenidades, capacidad, camas, tamaño,
 * destacada y un resumen corto. Todo es opcional para no romper habitaciones
 * existentes.
 */
class AddLandingFieldsToHotelRoomsTable extends Migration
{
    public function up()
    {
        Schema::table('hotel_rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('hotel_rooms', 'images')) {
                $table->text('images')->nullable()->after('description');
            }
            if (!Schema::hasColumn('hotel_rooms', 'amenities')) {
                $table->text('amenities')->nullable()->after('images');
            }
            if (!Schema::hasColumn('hotel_rooms', 'short_description')) {
                $table->string('short_description', 255)->nullable()->after('amenities');
            }
            if (!Schema::hasColumn('hotel_rooms', 'capacity')) {
                $table->unsignedInteger('capacity')->nullable()->after('short_description');
            }
            if (!Schema::hasColumn('hotel_rooms', 'beds')) {
                $table->string('beds', 100)->nullable()->after('capacity');
            }
            if (!Schema::hasColumn('hotel_rooms', 'size')) {
                $table->unsignedInteger('size')->nullable()->after('beds');
            }
            if (!Schema::hasColumn('hotel_rooms', 'featured')) {
                $table->boolean('featured')->default(false)->after('size');
            }
        });
    }

    public function down()
    {
        Schema::table('hotel_rooms', function (Blueprint $table) {
            foreach (['images', 'amenities', 'short_description', 'capacity', 'beds', 'size', 'featured'] as $column) {
                if (Schema::hasColumn('hotel_rooms', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
