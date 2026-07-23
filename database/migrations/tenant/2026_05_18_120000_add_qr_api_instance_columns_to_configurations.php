<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQrApiInstanceColumnsToConfigurations extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('configurations', 'qr_api_instance')) {
                $table->string('qr_api_instance')->nullable();
            }
            if (!Schema::hasColumn('configurations', 'qr_api_connected_phone')) {
                $table->string('qr_api_connected_phone', 30)->nullable();
            }
            if (!Schema::hasColumn('configurations', 'qr_api_profile_name')) {
                $table->string('qr_api_profile_name')->nullable();
            }
            if (!Schema::hasColumn('configurations', 'qr_api_connection_state')) {
                $table->string('qr_api_connection_state', 20)->default('disconnected');
            }
            if (!Schema::hasColumn('configurations', 'qr_api_connected_at')) {
                $table->timestamp('qr_api_connected_at')->nullable();
            }
            if (!Schema::hasColumn('configurations', 'qr_api_webhook_token')) {
                $table->string('qr_api_webhook_token', 64)->nullable();
            }
            if (!Schema::hasColumn('configurations', 'qr_api_use_bot_instance')) {
                $table->boolean('qr_api_use_bot_instance')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn([
                'qr_api_instance',
                'qr_api_connected_phone',
                'qr_api_profile_name',
                'qr_api_connection_state',
                'qr_api_connected_at',
                'qr_api_webhook_token',
                'qr_api_use_bot_instance',
            ]);
        });
    }
}
