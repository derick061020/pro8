<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_rel_suscription_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('user_rel_suscription_plans', 'orders_created')) {
                $table->integer('orders_created')->default(0)
                      ->comment('Cantidad de órdenes de cobro creadas para esta suscripción');
            }
        });
    }

    public function down()
    {
        Schema::table('user_rel_suscription_plans', function (Blueprint $table) {
            if (Schema::hasColumn('user_rel_suscription_plans', 'orders_created')) {
                $table->dropColumn('orders_created');
            }
        });
    }
};
