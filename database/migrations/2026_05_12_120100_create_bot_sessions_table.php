<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBotSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('bot_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('customer_phone', 30)->index();
            $table->enum('state', ['idle', 'active', 'awaiting_confirm', 'paused', 'closed'])->default('idle');
            $table->json('context_json')->nullable();
            $table->json('pending_document_json')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bot_sessions');
    }
}
