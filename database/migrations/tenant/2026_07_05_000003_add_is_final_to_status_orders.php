<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsFinalToStatusOrders extends Migration
{
    /**
     * Agrega el flag "estado final" (is_final).
     * Junto con is_initial, solo aplica al grupo de pedido (is_order_status)
     * y es único: solo un estado puede ser inicial y solo uno puede ser final.
     */
    public function up()
    {
        Schema::table('status_orders', function (Blueprint $table) {
            $table->boolean('is_final')->default(false)->after('is_initial');
        });
    }

    public function down()
    {
        Schema::table('status_orders', function (Blueprint $table) {
            $table->dropColumn('is_final');
        });
    }
}
