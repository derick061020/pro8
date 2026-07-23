<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShippingStatusOrderIdToOrders extends Migration
{
    /**
     * Agrega la columna del estado de envío al pedido.
     * Es independiente de status_order_id (pedido) y payment_status_order_id (pago).
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('shipping_status_order_id')->nullable()->after('payment_status_order_id');
            $table->foreign('shipping_status_order_id')->references('id')->on('status_orders');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shipping_status_order_id']);
            $table->dropColumn('shipping_status_order_id');
        });
    }
}
