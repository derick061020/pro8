<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Backfill de series.in_use para tenants existentes.
 *
 * Marca in_use=true en toda serie que ya tenga comprobantes emitidos, mirando la tabla
 * correspondiente segun el tipo de documento:
 *   - documents      (facturas, boletas, NC, ND, etc.)  -> match por document_type_id + series
 *   - dispatches     (guias 09 / 31)                     -> match por document_type_id + series
 *   - sale_notes     (nota de venta 80)                  -> match por series (siempre tipo 80)
 *
 * Idempotente: solo activa el flag, nunca lo desactiva.
 */
class TenantBackfillSeriesInUse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('documents')) {
            DB::statement("
                UPDATE series s
                SET s.in_use = 1
                WHERE EXISTS (
                    SELECT 1 FROM documents d
                    WHERE d.document_type_id = s.document_type_id AND d.series = s.number
                )
            ");
        }

        if (Schema::hasTable('dispatches')) {
            DB::statement("
                UPDATE series s
                SET s.in_use = 1
                WHERE EXISTS (
                    SELECT 1 FROM dispatches dp
                    WHERE dp.document_type_id = s.document_type_id AND dp.series = s.number
                )
            ");
        }

        if (Schema::hasTable('sale_notes')) {
            DB::statement("
                UPDATE series s
                SET s.in_use = 1
                WHERE s.document_type_id = '80' AND EXISTS (
                    SELECT 1 FROM sale_notes sn WHERE sn.series = s.number
                )
            ");
        }
    }

    /**
     * Reverse the migrations.
     *
     * No revertimos el flag: dejar in_use no afecta a un esquema sin la columna
     * (la columna se elimina en la migracion 000002 down).
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
