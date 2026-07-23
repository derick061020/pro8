<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentStatusOrderIdToOrders extends Migration
{
    /**
     * Agrega la columna del estado de pago al pedido.
     * El estado de pedido ya existente vive en status_order_id; este es independiente.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('payment_status_order_id')->nullable()->after('status_order_id');
            $table->foreign('payment_status_order_id')->references('id')->on('status_orders');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['payment_status_order_id']);
            $table->dropColumn('payment_status_order_id');
        });
    }
}
