<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Amplía offline_configurations para soportar el modo cliente (terminal Windows)
 * y el modo servidor (instalación online) del motor de sincronización.
 */
class TenantOfflineUpgradeConfigurationsTable extends Migration
{
    public function up()
    {
        Schema::table('offline_configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('offline_configurations', 'mode')) {
                // client = terminal Windows instalado en el local
                // server = instalación online que recibe la sincronización
                $table->string('mode', 10)->default('server')->after('is_client');
            }
            if (!Schema::hasColumn('offline_configurations', 'terminal_code')) {
                $table->string('terminal_code', 20)->nullable()->after('mode');
            }
            if (!Schema::hasColumn('offline_configurations', 'terminal_name')) {
                $table->string('terminal_name', 100)->nullable()->after('terminal_code');
            }
            if (!Schema::hasColumn('offline_configurations', 'sync_enabled')) {
                $table->boolean('sync_enabled')->default(false)->after('url_server');
            }
            if (!Schema::hasColumn('offline_configurations', 'sync_interval')) {
                // segundos entre ciclos automáticos de sincronización
                $table->unsignedInteger('sync_interval')->default(60)->after('sync_enabled');
            }
            if (!Schema::hasColumn('offline_configurations', 'is_online')) {
                $table->boolean('is_online')->default(false)->after('sync_interval');
            }
            if (!Schema::hasColumn('offline_configurations', 'last_ping_at')) {
                $table->timestamp('last_ping_at')->nullable()->after('is_online');
            }
            if (!Schema::hasColumn('offline_configurations', 'last_push_at')) {
                $table->timestamp('last_push_at')->nullable()->after('last_ping_at');
            }
            if (!Schema::hasColumn('offline_configurations', 'last_pull_at')) {
                $table->timestamp('last_pull_at')->nullable()->after('last_push_at');
            }
            if (!Schema::hasColumn('offline_configurations', 'git_remote')) {
                $table->string('git_remote')->nullable()->after('last_pull_at');
            }
            if (!Schema::hasColumn('offline_configurations', 'git_branch')) {
                $table->string('git_branch', 100)->nullable()->after('git_remote');
            }
            if (!Schema::hasColumn('offline_configurations', 'app_version')) {
                $table->string('app_version', 40)->nullable()->after('git_branch');
            }
        });

        // Las instalaciones existentes ya marcadas como cliente conservan ese rol.
        DB::table('offline_configurations')->where('is_client', true)->update(['mode' => 'client']);
    }

    public function down()
    {
        Schema::table('offline_configurations', function (Blueprint $table) {
            $columns = [
                'mode', 'terminal_code', 'terminal_name', 'sync_enabled', 'sync_interval',
                'is_online', 'last_ping_at', 'last_push_at', 'last_pull_at',
                'git_remote', 'git_branch', 'app_version',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('offline_configurations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
