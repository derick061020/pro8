<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemUnitTypePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_unit_type_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_unit_type_id');
            $table->tinyInteger('position');
            $table->decimal('price', 12, 2)->default(0);
            $table->string('label', 50);
            $table->boolean('is_active')->default(true);

            $table->foreign('item_unit_type_id')->references('id')->on('item_unit_types')->onDelete('cascade');
            $table->index(['item_unit_type_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_unit_type_prices');
    }
}
