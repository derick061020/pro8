<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWhatsappBotColumnsToConfigurations extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->string('evolution_url')->nullable();
            $table->string('evolution_apikey')->nullable();
            $table->string('evolution_instance')->nullable();
            $table->boolean('evolution_enabled')->default(false);
            $table->string('evolution_webhook_token', 64)->nullable();
            $table->string('openai_api_key')->nullable();
            $table->string('openai_model', 64)->default('gpt-4o-mini');
            $table->string('bot_trigger_command', 20)->default('/bot');
            $table->string('bot_exit_command', 20)->default('/fin');
            $table->unsignedInteger('bot_session_ttl_minutes')->default(30);
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn([
                'evolution_url',
                'evolution_apikey',
                'evolution_instance',
                'evolution_enabled',
                'evolution_webhook_token',
                'openai_api_key',
                'openai_model',
                'bot_trigger_command',
                'bot_exit_command',
                'bot_session_ttl_minutes',
            ]);
        });
    }
}
