<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropWhatsappBotFromSystem extends Migration
{
    public function up()
    {
        Schema::dropIfExists('bot_messages');
        Schema::dropIfExists('bot_sessions');
        Schema::dropIfExists('bot_whitelist');

        if (Schema::hasTable('configurations')) {
            Schema::table('configurations', function (Blueprint $table) {
                foreach ([
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
                ] as $col) {
                    if (Schema::hasColumn('configurations', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }

    public function down()
    {
        // Irreversible: las columnas y tablas viven ahora en cada tenant.
    }
}
