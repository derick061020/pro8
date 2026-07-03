<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_notes', function (Blueprint $table) {
            $table->char('document_type_id', 2)->default('');
            $table->char('series', 4)->default('');
            $table->integer('number')->default(0);
            $table->integer('order_zone_id')->nullable();
            $table->integer('order_person_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('order_notes', function (Blueprint $table) {
            $table->dropColumn([
                'document_type_id',
                'series',
                'number',
                'order_zone_id',
                'order_person_id',
            ]);
        });
    }
};
