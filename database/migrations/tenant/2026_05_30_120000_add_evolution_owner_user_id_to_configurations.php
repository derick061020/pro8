<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEvolutionOwnerUserIdToConfigurations extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            // Usuario que conecto la instancia del bot. Se usa para identificar
            // al "dueño" cuando habla consigo mismo (self-chat: remoteJid igual
            // al numero conectado) y autorizarlo sin pasar por la whitelist.
            $table->unsignedInteger('evolution_owner_user_id')->nullable()->after('evolution_enabled');
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('evolution_owner_user_id');
        });
    }
}
