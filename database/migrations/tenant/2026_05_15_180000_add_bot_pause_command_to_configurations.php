<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBotPauseCommandToConfigurations extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->string('bot_pause_command', 20)->default('/p')->after('bot_exit_command');
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('bot_pause_command');
        });
    }
}
