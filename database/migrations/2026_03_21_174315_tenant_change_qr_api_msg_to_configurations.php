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
        Schema::table('configurations', function (Blueprint $table) {
            $table->text('qr_api_msg')->change();
        });

        Schema::table('payment_orders', function (Blueprint $table){
            $table->text('payment_link')->nullable();
            $table->unsignedBigInteger('link_expires_unix_timestamp')->nullable();
            $table->timestamp('link_expires_timestamp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            // $table->string('qr_api_msg')->change();
        });
    }
};
