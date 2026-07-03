<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_settlements', function (Blueprint $table) {
            $table->json('soap_shipping_response')->nullable();
        });
    }

    public function down()
    {
        Schema::table('purchase_settlements', function (Blueprint $table) {
            $table->dropColumn('soap_shipping_response');
        });
    }
};
