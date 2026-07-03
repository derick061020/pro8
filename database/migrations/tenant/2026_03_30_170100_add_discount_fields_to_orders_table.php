<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountFieldsToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_discount', 12, 2)->default(0)->after('total');
            $table->string('discount_coupon_code')->nullable()->after('total_discount');
            $table->unsignedBigInteger('discount_coupon_id')->nullable()->after('discount_coupon_code');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['total_discount', 'discount_coupon_code', 'discount_coupon_id']);
        });
    }
}
