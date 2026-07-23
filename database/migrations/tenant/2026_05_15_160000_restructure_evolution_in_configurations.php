<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RestructureEvolutionInConfigurations extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            if (Schema::hasColumn('configurations', 'evolution_url')) {
                $table->dropColumn('evolution_url');
            }
            if (Schema::hasColumn('configurations', 'evolution_apikey')) {
                $table->dropColumn('evolution_apikey');
            }
        });

        Schema::table('configurations', function (Blueprint $table) {
            $table->string('evolution_connection_state', 20)->default('disconnected')->after('evolution_instance');
            $table->timestamp('evolution_connected_at')->nullable()->after('evolution_connection_state');
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn(['evolution_connection_state', 'evolution_connected_at']);
            $table->string('evolution_url')->nullable();
            $table->string('evolution_apikey')->nullable();
        });
    }
}
