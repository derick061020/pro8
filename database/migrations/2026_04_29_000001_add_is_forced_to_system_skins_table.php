<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsForcedToSystemSkinsTable extends Migration
{
    public function up()
    {
        Schema::connection('system')->table('system_skins', function (Blueprint $table) {
            $table->boolean('is_forced')->default(false)->after('is_tenant_default');
        });
    }

    public function down()
    {
        Schema::connection('system')->table('system_skins', function (Blueprint $table) {
            $table->dropColumn('is_forced');
        });
    }
}
