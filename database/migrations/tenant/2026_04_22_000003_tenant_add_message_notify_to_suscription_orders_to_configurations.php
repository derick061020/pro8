<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('configurations', 'message_notify_to_suscription_orders')) {
                $table->text('message_notify_to_suscription_orders')->nullable();
            }

            if (!Schema::hasColumn('configurations', 'send_link_subscription')) {
                $table->boolean('send_link_subscription')->default(false);
            }

            if (!Schema::hasColumn('configurations', 'count_send_link_subscription')) {
                $table->integer('count_send_link_subscription')->default(0);
            }
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            foreach (['message_notify_to_suscription_orders', 'send_link_subscription', 'count_send_link_subscription'] as $col) {
                if (Schema::hasColumn('configurations', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
