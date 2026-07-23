<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEvolutionServerToSystemConfigurations extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->string('evolution_server_url')->nullable();
            $table->string('evolution_server_apikey')->nullable();
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn(['evolution_server_url', 'evolution_server_apikey']);
        });
    }
}
