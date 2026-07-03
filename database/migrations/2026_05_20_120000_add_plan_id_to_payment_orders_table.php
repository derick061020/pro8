<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlanIdToPaymentOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            $table->unsignedInteger('plan_id')->nullable()->after('client_id');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn('plan_id');
        });
    }
}
