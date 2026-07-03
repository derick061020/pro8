<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('keyclient_contaweb')->nullable()->after('start_billing_cycle');
            $table->boolean('contaweb_url_access_enabled')->default(false)->after('keyclient_contaweb');
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('keyclient_contaweb');
            $table->dropColumn('contaweb_url_access_enabled');
        });
    }
};
