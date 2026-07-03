<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionToClaimChannelsTable extends Migration
{
    public function up()
    {
        Schema::table('claim_channels', function (Blueprint $table) {
            $table->string('description', 255)->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('claim_channels', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}
