<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->char('document_type_id', 2)->default('');
            $table->char('series', 4)->default('');
            $table->integer('number')->default(0);
        });
    }

    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['document_type_id', 'series', 'number']);
        });
    }
};
