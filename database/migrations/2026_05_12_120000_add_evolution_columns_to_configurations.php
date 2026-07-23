<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEvolutionColumnsToConfigurations extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->string('evolution_url')->nullable()->default(null);
            $table->string('evolution_apikey')->nullable()->default(null)->after('evolution_url');
            $table->string('evolution_instance')->nullable()->default(null)->after('evolution_apikey');
            $table->boolean('evolution_enabled')->default(false)->after('evolution_instance');
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn(['evolution_url', 'evolution_apikey', 'evolution_instance', 'evolution_enabled']);
        });
    }
}
