<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddIsTenantDefaultToSystemSkinsTable extends Migration
{
    public function up()
    {
        Schema::connection('system')->table('system_skins', function (Blueprint $table) {
            $table->boolean('is_tenant_default')->default(false)->after('is_default');
        });

        DB::connection('system')->table('system_skins')->where('id', 3)->update(['is_tenant_default' => true]);
    }

    public function down()
    {
        Schema::connection('system')->table('system_skins', function (Blueprint $table) {
            $table->dropColumn('is_tenant_default');
        });
    }
}
