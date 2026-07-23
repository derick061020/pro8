<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class BackfillPaymentStatusOnOrders extends Migration
{
    public function up()
    {
        $paymentIds = DB::table('status_orders')->where('is_payment_status', true)->pluck('id')->all();
        $orderIds   = DB::table('status_orders')->where('is_order_status', true)->pluck('id')->all();

        $initialOrder = DB::table('status_orders')
            ->where('is_order_status', true)
            ->where('is_initial', true)
            ->orderBy('sort_order')
            ->first();
        $initialOrderId = $initialOrder->id ?? null;

        $verifiedPayment = DB::table('status_orders')
            ->where('is_payment_status', true)
            ->where('description', 'Pago verificado')
            ->first();
        if (!$verifiedPayment) {
            $verifiedPayment = DB::table('status_orders')
                ->where('is_payment_status', true)
                ->where('is_initial', false)
                ->orderByDesc('sort_order')
                ->first();
        }
        $verifiedPaymentId = $verifiedPayment->id ?? null;

        if (!empty($paymentIds)) {
            DB::table('orders')
                ->whereIn('status_order_id', $paymentIds)
                ->whereNull('payment_status_order_id')
                ->update(['payment_status_order_id' => DB::raw('status_order_id')]);

            DB::table('orders')
                ->whereIn('status_order_id', $paymentIds)
                ->update(['status_order_id' => $initialOrderId]);
        }

        if (!empty($orderIds) && $verifiedPaymentId) {
            DB::table('orders')
                ->whereIn('status_order_id', $orderIds)
                ->whereNull('payment_status_order_id')
                ->update(['payment_status_order_id' => $verifiedPaymentId]);
        }
    }

    /**
     * No es reversible de forma segura: la asignación original (un solo estado por pedido)
     * se pierde al dividirse en dos columnas. Se deja como no-op intencional.
     */
    public function down()
    {
        // Sin reversa: backfill de datos.
    }
}
