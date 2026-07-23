<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBotMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('bot_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id')->nullable()->index();
            $table->enum('direction', ['in', 'out']);
            $table->string('phone', 30)->index();
            $table->text('body')->nullable();
            $table->string('message_id')->nullable()->index();
            $table->boolean('from_me')->default(false);
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bot_messages');
    }
}
