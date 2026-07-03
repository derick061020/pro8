<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->decimal('quantity_factor', 12, 4)->default(1);
            $table->longText('name_product_pdf')->nullable();
        });
    }

    public function down()
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn(['quantity_factor', 'name_product_pdf']);
        });
    }
};
