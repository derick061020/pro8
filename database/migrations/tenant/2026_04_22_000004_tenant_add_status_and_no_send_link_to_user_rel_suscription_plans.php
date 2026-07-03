<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_rel_suscription_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('user_rel_suscription_plans', 'subscription_status')) {
                $table->enum('subscription_status', ['authorized', 'paused', 'cancelled', 'finished'])
                      ->nullable()
                      ->after('start_date');
            }

            if (!Schema::hasColumn('user_rel_suscription_plans', 'no_send_link')) {
                $table->boolean('no_send_link')->default(false)->after('subscription_status');
            }

            if (!Schema::hasColumn('user_rel_suscription_plans', 'total_send_link')) {
                $table->integer('total_send_link')->default(0)->after('no_send_link');
            }
        });
    }

    public function down()
    {
        Schema::table('user_rel_suscription_plans', function (Blueprint $table) {
            if (Schema::hasColumn('user_rel_suscription_plans', 'total_send_link')) {
                $table->dropColumn('total_send_link');
            }
            if (Schema::hasColumn('user_rel_suscription_plans', 'no_send_link')) {
                $table->dropColumn('no_send_link');
            }
            if (Schema::hasColumn('user_rel_suscription_plans', 'subscription_status')) {
                $table->dropColumn('subscription_status');
            }
        });
    }
};
