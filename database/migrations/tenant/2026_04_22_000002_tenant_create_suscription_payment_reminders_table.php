<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('suscription_payment_reminders')) {
            Schema::create('suscription_payment_reminders', function (Blueprint $table) {
                $table->id();
                $table->integer('reminder_days')->default(0);
                $table->enum('reminder_type', ['before', 'same_day', 'after'])->default('before');
                $table->time('reminder_time');
                $table->enum('shipping_medium', ['email', 'whatsapp'])->default('email');
            });
        }

        Schema::table('configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('configurations', 'before_day_creation_suscription_order')) {
                $table->integer('before_day_creation_suscription_order')->default(1);
            }
        });
    }

    public function down()
    {
        Schema::dropIfExists('suscription_payment_reminders');

        Schema::table('configurations', function (Blueprint $table) {
            if (Schema::hasColumn('configurations', 'before_day_creation_suscription_order')) {
                $table->dropColumn('before_day_creation_suscription_order');
            }
        });
    }
};
