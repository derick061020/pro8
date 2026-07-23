<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddUniqueItemWarehouseConstraint extends Migration
{
    /**
     * Antes de poner el UNIQUE, consolidamos filas duplicadas: dejamos la fila
     * con MIN(id) (que refleja las ventas/movimientos historicos) y borramos
     * las demas. Sin este cleanup la migracion fallaria en tenants que ya
     * tengan duplicados (ej. demos.pro8.test).
     */
    public function up()
    {
        DB::statement("
            DELETE iw FROM item_warehouse iw
            JOIN (
                SELECT MIN(id) AS keep_id, item_id, warehouse_id
                FROM item_warehouse
                GROUP BY item_id, warehouse_id
                HAVING COUNT(*) > 1
            ) keep
              ON keep.item_id = iw.item_id
             AND keep.warehouse_id = iw.warehouse_id
             AND iw.id > keep.keep_id
        ");

        Schema::table('item_warehouse', function (Blueprint $table) {
            $table->unique(['item_id', 'warehouse_id'], 'item_warehouse_item_id_warehouse_id_unique');
        });
    }

    public function down()
    {
        Schema::table('item_warehouse', function (Blueprint $table) {
            $table->dropUnique('item_warehouse_item_id_warehouse_id_unique');
        });
    }
}
