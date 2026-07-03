<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountCouponUsagesTable extends Migration
{
    public function up()
    {
        Schema::create('discount_coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('discount_coupon_id');
            $table->unsignedInteger('person_id')->nullable();
            $table->unsignedInteger('order_id')->nullable();
            $table->timestamps();

            $table->foreign('discount_coupon_id')->references('id')->on('discount_coupons')->onDelete('cascade');
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('set null');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('discount_coupon_usages');
    }
}
