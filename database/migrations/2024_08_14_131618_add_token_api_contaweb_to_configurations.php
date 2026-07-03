<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->string('token_api_contaweb')->nullable();
            $table->string('url_api_contaweb')->nullable();
            $table->string('url_web_contaweb')->nullable();
            $table->string('server_ip_contaweb')->nullable();
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('token_api_contaweb');
            $table->dropColumn('url_api_contaweb');
            $table->dropColumn('url_web_contaweb');
            $table->dropColumn('server_ip_contaweb');
        });
    }
};
