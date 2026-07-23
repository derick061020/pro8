<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEvolutionWebhookTokenToConfigurations extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->string('evolution_webhook_token', 64)->nullable()->after('evolution_enabled');
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('evolution_webhook_token');
        });
    }
}
