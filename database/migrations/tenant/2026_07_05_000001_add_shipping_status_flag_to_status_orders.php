<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShippingStatusFlagToStatusOrders extends Migration
{
    /**
     * Agrega el flag de tipo "estado de envío" (is_shipping_status).
     * Es mutuamente excluyente con is_payment_status e is_order_status:
     * un estado pertenece a una sola columna del listado de pedidos
     * (pago, pedido o envío).
     */
    public function up()
    {
        Schema::table('status_orders', function (Blueprint $table) {
            $table->boolean('is_shipping_status')->default(false)->after('is_order_status');
        });
    }

    public function down()
    {
        Schema::table('status_orders', function (Blueprint $table) {
            $table->dropColumn('is_shipping_status');
        });
    }
}
