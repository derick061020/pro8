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
            $table->string('license_plate', 20)->nullable()->after('notes')->comment('Vehicle license plate');
            $table->string('travel_reason', 50)->nullable()->after('license_plate')->comment('Reason for travel: negocios, turismo, visita_familiar, tramites, otros');
            $table->integer('adults')->default(1)->after('travel_reason')->comment('Number of adults');
            $table->integer('children')->default(0)->after('adults')->comment('Number of children');
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
            $table->dropColumn(['license_plate', 'travel_reason', 'adults', 'children']);
        });
    }
};
