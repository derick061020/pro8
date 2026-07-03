<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddIsSystemToSkinsTable extends Migration
{
    public function up()
    {
        Schema::table('skins', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('status');
        });

        DB::table('skins')->whereIn('filename', ['default.css', 'light.css', 'black.css', 'modern.css'])
            ->update(['is_system' => true]);
    }

    public function down()
    {
        Schema::table('skins', function (Blueprint $table) {
            $table->dropColumn('is_system');
        });
    }
}
