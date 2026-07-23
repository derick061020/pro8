<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBotWhitelistTable extends Migration
{
    public function up()
    {
        Schema::create('bot_whitelist', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number', 30)->unique();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('seller_name')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bot_whitelist');
    }
}
