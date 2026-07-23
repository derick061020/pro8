<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddIndexesToInventoryKardex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_kardex', function (Blueprint $table) {
            // Cubre cálculo de stock y reportes de kardex:
            // where item_id + warehouse_id (+ date_of_issue < ?) -> sum(quantity)
            $table->index(['item_id', 'warehouse_id', 'date_of_issue'], 'inventory_kardex_item_wh_date_index');

            // Cubre el eager loading polimórfico: with('inventory_kardexable')
            $table->index(['inventory_kardexable_type', 'inventory_kardexable_id'], 'inventory_kardex_kardexable_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_kardex', function (Blueprint $table) {
            $table->dropIndex('inventory_kardex_item_wh_date_index');
            $table->dropIndex('inventory_kardex_kardexable_index');
        });
    }
}
