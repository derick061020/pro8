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
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'test_days_enabled')) {
                $table->boolean('test_days_enabled')->default(false);
            }
            if (!Schema::hasColumn('plans', 'test_days')) {
                $table->integer('test_days')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'test_days_enabled')) {
                $table->dropColumn('test_days_enabled');
            }
            if (Schema::hasColumn('plans', 'test_days')) {
                $table->dropColumn('test_days');
            }
        });
    }
};
