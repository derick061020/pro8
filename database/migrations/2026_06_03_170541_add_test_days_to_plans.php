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
<<<<<<<< HEAD:database/migrations/tenant/2026_07_14_100000_tenant_add_reservation_origin_to_hotel_rents_table.php
        Schema::table('hotel_rents', function (Blueprint $table) {
            if (!Schema::hasColumn('hotel_rents', 'reservation_origin')) {
                $table->string('reservation_origin', 30)->nullable()->after('travel_reason')
                    ->comment('Medio por el que se realizó la reserva: whatsapp, correo, celular, presencial');
            }
========
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('test_days_enabled')->default(false);
            $table->integer('test_days')->nullable();
>>>>>>>> vendor-pro9:database/migrations/2026_06_03_170541_add_test_days_to_plans.php
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
<<<<<<<< HEAD:database/migrations/tenant/2026_07_14_100000_tenant_add_reservation_origin_to_hotel_rents_table.php
        Schema::table('hotel_rents', function (Blueprint $table) {
            if (Schema::hasColumn('hotel_rents', 'reservation_origin')) {
                $table->dropColumn('reservation_origin');
            }
========
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('test_days_enabled');
            $table->dropColumn('test_days');
>>>>>>>> vendor-pro9:database/migrations/2026_06_03_170541_add_test_days_to_plans.php
        });
    }
};
