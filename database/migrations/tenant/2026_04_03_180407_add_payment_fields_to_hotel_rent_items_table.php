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
        Schema::table('hotel_rent_items', function (Blueprint $table) {
            $table->decimal('quantity', 12, 4)->default(1)->after('hotel_rent_order_id');
            $table->decimal('unit_price', 12, 4)->default(0)->after('quantity');
            $table->decimal('total', 12, 4)->default(0)->after('unit_price');
            $table->text('description')->nullable()->after('total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotel_rent_items', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'unit_price', 'total', 'description']);
        });
    }
};
