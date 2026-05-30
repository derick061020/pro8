<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddIsReserveToHotelRentsTable extends Migration
{
    public function up()
    {
        Schema::table('hotel_rents', function (Blueprint $table) {
            $table->boolean('is_reserve')->default(false)->after('status');
        });
    }

    public function down()
    {
        Schema::table('hotel_rents', function (Blueprint $table) {
            $table->dropColumn('is_reserve');
        });
    }
}
