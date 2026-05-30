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
            $table->dateTime('rental_date_time')->nullable()->after('output_time')->index();
            $table->decimal('rental_price', 12, 2)->nullable()->after('rental_date_time');
            $table->string('rental_period_type')->nullable()->after('rental_price'); // 'hour', 'day', 'month'
            $table->boolean('is_reserve')->default(false)->after('rental_period_type')->index();
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
            $table->dropColumn(['rental_date_time', 'rental_price', 'rental_period_type', 'is_reserve']);
        });
    }
};
