<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEvolutionProfileToConfigurations extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->string('evolution_connected_phone', 30)->nullable()->after('evolution_instance');
            $table->string('evolution_profile_name')->nullable()->after('evolution_connected_phone');
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn(['evolution_connected_phone', 'evolution_profile_name']);
        });
    }
}
