<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AddApplyInPeriodToItemRelSuscriptionPlans extends Migration
{
    /**
     * Run the migrations.
     * Añade el campo `apply_in_period` a `item_rel_suscription_plans`.
     * Este campo determina en qué periodo se aplica el item:
     * - 'all': Cobrar en todos los períodos (default)
     * - 'first': Cobrar solo en el primer cobro (solo para planes ilimitados)
     * - '1', '2', '3', etc: Cobrar en un período específico
     */
    public function up()
    {
        Schema::table('item_rel_suscription_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('item_rel_suscription_plans', 'apply_in_period')) {
                $table->string('apply_in_period')->default('all')->after('warehouse_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('item_rel_suscription_plans', function (Blueprint $table) {
            if (Schema::hasColumn('item_rel_suscription_plans', 'apply_in_period')) {
                $table->dropColumn('apply_in_period');
            }
        });
    }
}
