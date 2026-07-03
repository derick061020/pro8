<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsPopularToPlansTable extends Migration
{
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('is_popular')->default(false)->after('pricing');
        });
    }

    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('is_popular');
        });
    }
}
