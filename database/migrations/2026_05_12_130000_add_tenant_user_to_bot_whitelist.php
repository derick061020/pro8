<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTenantUserToBotWhitelist extends Migration
{
    public function up()
    {
        Schema::table('bot_whitelist', function (Blueprint $table) {
            $table->string('tenant_hostname')->nullable()->after('phone_number')->index();
            $table->unsignedBigInteger('user_id')->nullable()->after('tenant_hostname');
        });
    }

    public function down()
    {
        Schema::table('bot_whitelist', function (Blueprint $table) {
            $table->dropColumn(['tenant_hostname', 'user_id']);
        });
    }
}
