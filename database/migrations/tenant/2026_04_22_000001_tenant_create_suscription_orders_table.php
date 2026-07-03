<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('suscription_orders')) {
            return;
        }

        Schema::create('suscription_orders', function (Blueprint $table) {
            $table->id();
            $table->string('external_id', 6)->nullable()->unique();
            $table->string('number', 20)->nullable();
            $table->unsignedBigInteger('suscription_id')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamp('date_of_issue')->nullable();
            $table->timestamp('date_of_due')->nullable();
            $table->timestamp('date_of_payment')->nullable();
            $table->enum('status', ['pending', 'paid', 'canceled', 'rejected', 'expired'])->default('pending');
            $table->string('documentable_type')->nullable();
            $table->unsignedBigInteger('documentable_id')->nullable();
            $table->integer('count_notification')->default(0);
            $table->timestamp('date_notification')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('suscription_orders');
    }
};
