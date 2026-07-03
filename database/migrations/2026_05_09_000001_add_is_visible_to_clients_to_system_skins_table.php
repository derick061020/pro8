<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsVisibleToClientsToSystemSkinsTable extends Migration
{
    public function up()
    {
        Schema::table('system_skins', function (Blueprint $table) {
            $table->boolean('is_visible_to_clients')->default(true)->after('is_forced');
        });
    }

    public function down()
    {
        Schema::table('system_skins', function (Blueprint $table) {
            $table->dropColumn('is_visible_to_clients');
        });
    }
}
