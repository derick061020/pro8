<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant_tasks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            $table->string('uuid_tenant')->nullable();
            $table->string('class');
            $table->string('execution_time');
            $table->text('output')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenant_tasks');
    }
};
