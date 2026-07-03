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
        Schema::table('public_search_customizations', function (Blueprint $table) {
            $table->string('background_image_path')->nullable()->after('background_color');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public_search_customizations', function (Blueprint $table) {
            $table->dropColumn('background_image_path');
        });
    }
};

