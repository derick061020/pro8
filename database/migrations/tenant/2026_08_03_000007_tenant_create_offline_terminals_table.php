<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registro de terminales offline. Vive en el servidor online: cada PC Windows
 * se da de alta aquí en el pareo y desde acá se monitorea.
 */
class TenantCreateOfflineTerminalsTable extends Migration
{
    public function up()
    {
        Schema::create('offline_terminals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 20)->unique();
            $table->string('name', 100)->nullable();
            $table->unsignedInteger('establishment_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();

            $table->boolean('active')->default(true);
            $table->string('app_version', 40)->nullable();
            $table->string('last_ip', 45)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('last_push_at')->nullable();
            $table->timestamp('last_pull_at')->nullable();
            $table->unsignedInteger('pending_hint')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('offline_terminals');
    }
}
