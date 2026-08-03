<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marca hasta dónde bajó el terminal cada entidad maestra desde el servidor,
 * para que el pull siguiente sea incremental y no traiga todo de nuevo.
 */
class TenantCreateOfflinePullStatesTable extends Migration
{
    public function up()
    {
        Schema::create('offline_pull_states', function (Blueprint $table) {
            $table->increments('id');
            $table->string('entity', 60)->unique();
            $table->timestamp('last_synced_at')->nullable();
            $table->unsignedBigInteger('last_remote_id')->nullable();
            $table->unsignedInteger('records')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('offline_pull_states');
    }
}
