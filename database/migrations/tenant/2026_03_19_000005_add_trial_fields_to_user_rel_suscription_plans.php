<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrialFieldsToUserRelSuscriptionPlans extends Migration
{
    /**
     * Añade los campos de período de prueba a la tabla de suscripciones asignadas.
     * - trial_start_date: fecha de inicio del período de prueba
     * - trial_days: número de días de prueba
     */
    public function up()
    {
        Schema::table('user_rel_suscription_plans', function (Blueprint $table) {
            if (! Schema::hasColumn('user_rel_suscription_plans', 'trial_start_date')) {
                $table->date('trial_start_date')->nullable()->after('start_date');
            }
            if (! Schema::hasColumn('user_rel_suscription_plans', 'trial_days')) {
                $table->integer('trial_days')->nullable()->after('trial_start_date');
            }
        });
    }

    /**
     * Revierte los cambios de la migración.
     */
    public function down()
    {
        Schema::table('user_rel_suscription_plans', function (Blueprint $table) {
            if (Schema::hasColumn('user_rel_suscription_plans', 'trial_days')) {
                $table->dropColumn('trial_days');
            }
            if (Schema::hasColumn('user_rel_suscription_plans', 'trial_start_date')) {
                $table->dropColumn('trial_start_date');
            }
        });
    }
}
