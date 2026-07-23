<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixDefaultPaymentStatusOrders extends Migration
{
    public function up()
    {
        DB::table('status_orders')
            ->whereIn('description', ['Pago sin verificar', 'Pago verificado'])
            ->where('is_payment_status', false)
            ->update([
                'is_payment_status' => true,
                'is_order_status'   => false,
            ]);
    }

    public function down()
    {
        DB::table('status_orders')
            ->whereIn('description', ['Pago sin verificar', 'Pago verificado'])
            ->where('is_payment_status', true)
            ->update([
                'is_payment_status' => false,
                'is_order_status'   => true,
            ]);
    }
}
