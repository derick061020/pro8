<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBotTriggerToConfigurations extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->string('bot_trigger_command', 30)->default('/bot')->after('openai_model');
            $table->string('bot_exit_command', 30)->default('/salir')->after('bot_trigger_command');
            $table->unsignedSmallInteger('bot_session_ttl_minutes')->default(30)->after('bot_exit_command');
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn(['bot_trigger_command', 'bot_exit_command', 'bot_session_ttl_minutes']);
        });
    }
}
