<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AddUnlimitedToSuscriptionPlans extends Migration
{
    /**
     * Run the migrations.
     * Añade el campo booleano `unlimited` a `suscription_plans`.
     */
    public function up()
    {
        Schema::table('suscription_plans', function (Blueprint $table) {
            if (! Schema::hasColumn('suscription_plans', 'unlimited')) {
                $table->boolean('unlimited')->default(false)->after('quantity_period');
            }
            if (! Schema::hasColumn('suscription_plans', 'trial_days')) {
                $table->integer('trial_days')->nullable()->after('unlimited');
            }
            if (! Schema::hasColumn('suscription_plans', 'status')) {
                $table->boolean('status')->default(true)->after('trial_days');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('suscription_plans', function (Blueprint $table) {
            if (Schema::hasColumn('suscription_plans', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('suscription_plans', 'trial_days')) {
                $table->dropColumn('trial_days');
            }
            if (Schema::hasColumn('suscription_plans', 'unlimited')) {
                $table->dropColumn('unlimited');
            }
        });
    }
}
