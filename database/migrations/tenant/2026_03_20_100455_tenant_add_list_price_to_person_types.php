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
        Schema::table('person_types', function (Blueprint $table) {
            $table->unsignedInteger('price_label_id')->nullable();
            $table->foreign('price_label_id')->references('id')->on('price_labels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('person_types', function (Blueprint $table) {
            $table->dropForeign(['price_label_id']);
            $table->dropColumn('price_label_id');
        });
    }
};
