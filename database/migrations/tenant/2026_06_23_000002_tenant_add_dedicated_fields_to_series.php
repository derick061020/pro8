<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Campos para series dedicadas y control de uso.
 *
 * - dedicated: marca la serie como dedicada (no se lista en emision salvo via grupo activo).
 * - series_device_group_id: grupo al que pertenece la serie dedicada (una serie en <=1 grupo).
 * - in_use: true la primera vez que se emite un comprobante sobre la serie. Evita consultar
 *   documents/sale_notes/dispatches por tipo para saber si esta en uso (bloqueo de correlativo
 *   y de borrado). Ver docs/series-grupos-dedicados-plan.md (§4.7, §7.2).
 */
class TenantAddDedicatedFieldsToSeries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('series', function (Blueprint $table) {
            if (! Schema::hasColumn('series', 'dedicated')) {
                $table->boolean('dedicated')->default(false)->after('contingency');
            }
            if (! Schema::hasColumn('series', 'series_device_group_id')) {
                $table->unsignedInteger('series_device_group_id')->nullable()->after('dedicated');
                $table->foreign('series_device_group_id')
                    ->references('id')->on('series_device_groups')
                    ->nullOnDelete();
            }
            if (! Schema::hasColumn('series', 'in_use')) {
                $table->boolean('in_use')->default(false)->after('series_device_group_id')
                    ->comment('True al emitir el primer comprobante en la serie');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('series', function (Blueprint $table) {
            if (Schema::hasColumn('series', 'series_device_group_id')) {
                $table->dropForeign(['series_device_group_id']);
                $table->dropColumn('series_device_group_id');
            }
            if (Schema::hasColumn('series', 'dedicated')) {
                $table->dropColumn('dedicated');
            }
            if (Schema::hasColumn('series', 'in_use')) {
                $table->dropColumn('in_use');
            }
        });
    }
}
