<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddTypeFlagsToStatusOrders extends Migration
{
    /**
     * Agrega los flags de tipo de estado: pago (is_payment_status) y pedido (is_order_status).
     * Son mutuamente excluyentes: un estado pertenece a una sola columna del listado de pedidos.
     *
     * Los registros existentes se marcan como estado de pedido (is_order_status = true) para
     * preservar el comportamiento actual, donde la única columna era el estado del pedido.
     */
    public function up()
    {
        Schema::table('status_orders', function (Blueprint $table) {
            $table->boolean('is_payment_status')->default(false)->after('is_initial');
            $table->boolean('is_order_status')->default(true)->after('is_payment_status');
        });

        DB::table('status_orders')->update([
            'is_payment_status' => false,
            'is_order_status'   => true,
        ]);

        DB::table('status_orders')
            ->whereIn('description', ['Pago sin verificar', 'Pago verificado'])
            ->update([
                'is_payment_status' => true,
                'is_order_status'   => false,
            ]);
    }

    public function down()
    {
        Schema::table('status_orders', function (Blueprint $table) {
            $table->dropColumn(['is_payment_status', 'is_order_status']);
        });
    }
}
