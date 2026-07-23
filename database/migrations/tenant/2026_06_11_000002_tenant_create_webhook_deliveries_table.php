<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_subscription_id')->constrained('webhook_subscriptions')->cascadeOnDelete();
            $table->string('event', 50)->index();
            $table->char('event_id', 36);
            $table->json('payload');
            $table->string('status', 10)->default('pending');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->smallInteger('response_code')->nullable();
            $table->text('response_body')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['webhook_subscription_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('webhook_deliveries');
    }
};
