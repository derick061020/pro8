<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomFilenameToSystemSkinsTable extends Migration
{
    public function up()
    {
        Schema::connection('system')->table('system_skins', function (Blueprint $table) {
            $table->string('custom_filename')->nullable()->after('filename');
        });
    }

    public function down()
    {
        Schema::connection('system')->table('system_skins', function (Blueprint $table) {
            $table->dropColumn('custom_filename');
        });
    }
}
