<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddActionFieldsToStatusOrders extends Migration
{
    /**
     * Agrega columnas de configuración visual y acciones automáticas a status_orders.
     * También actualiza los registros existentes con valores compatibles según el
     * comportamiento hardcodeado anterior (IDs 1-4 semilla).
     */
    public function up()
    {
        Schema::table('status_orders', function (Blueprint $table) {
            // Color visual del estado (hex, ej. "#4CAF50")
            $table->string('color', 7)->nullable()->after('description');

            // Posición en la lista (para drag-and-drop)
            $table->unsignedTinyInteger('sort_order')->default(0)->after('color');

            // Indica si este estado es el inicial para nuevos pedidos
            $table->boolean('is_initial')->default(false)->after('sort_order');

            // Acciones automáticas del sistema
            $table->boolean('action_generate_document')->default(false)->after('is_initial');
            $table->boolean('action_discount_stock')->default(false)->after('action_generate_document');
            $table->boolean('action_mark_payment')->default(false)->after('action_discount_stock');
            $table->boolean('action_send_email')->default(false)->after('action_mark_payment');
            $table->boolean('action_notify_dispatch')->default(false)->after('action_send_email');
            $table->boolean('action_generate_remission')->default(false)->after('action_notify_dispatch');
            $table->boolean('action_free_reserved_stock')->default(false)->after('action_generate_remission');
            $table->boolean('action_block_returns')->default(false)->after('action_free_reserved_stock');
            $table->boolean('action_void_order')->default(false)->after('action_block_returns');
        });

        /*
         * Asignación de flags a los registros existentes.
         *
         * Se recorren TODOS los registros presentes en la tabla (no solo los 4 semilla),
         * ya que en producción puede haber estados adicionales creados por los tenants.
         *
         * Lógica de asignación:
         * - id=1 (Pago sin verificar): estado inicial del flujo
         * - id=2 (Pago verificado): equivale al antiguo trigger hardcodeado para generar comprobante
         * - id=3 (Despachado): equivale al antiguo trigger hardcodeado para descontar stock
         * - id=4 (Confirmado) y cualquier otro: sin acciones automáticas, sort_order incremental
         */
        $statuses = DB::table('status_orders')->orderBy('id')->get();

        foreach ($statuses as $index => $status) {
            $data = [
                'sort_order' => $index,
                'is_initial' => false,
                'action_generate_document' => false,
                'action_discount_stock' => false,
                'action_mark_payment' => false,
                'action_send_email' => false,
                'action_notify_dispatch' => false,
                'action_generate_remission' => false,
                'action_free_reserved_stock' => false,
                'action_block_returns' => false,
                'action_void_order' => false,
                'color' => null,
            ];

            // Mapear los comportamientos hardcodeados anteriores a los flags correspondientes
            switch ($status->id) {
                case 1:
                    // Antes: estado por defecto al crear pedido
                    $data['is_initial'] = true;
                    $data['color'] = '#909399'; // gris
                    break;
                case 2:
                    // Antes: if (status_order_id == 2) → abrir modal de generación de comprobante
                    $data['action_generate_document'] = true;
                    $data['action_mark_payment'] = true;
                    $data['color'] = '#409EFF'; // azul
                    break;
                case 3:
                    // Antes: if (status_order_id == 3) → descontar stock en ItemWarehouse
                    $data['action_discount_stock'] = true;
                    $data['action_notify_dispatch'] = true;
                    $data['color'] = '#E6A23C'; // ámbar
                    break;
                case 4:
                    $data['color'] = '#67C23A'; // verde
                    break;
            }

            DB::table('status_orders')->where('id', $status->id)->update($data);
        }
    }

    /**
     * Revierte la migración eliminando las columnas agregadas.
     */
    public function down()
    {
        Schema::table('status_orders', function (Blueprint $table) {
            $table->dropColumn([
                'color',
                'sort_order',
                'is_initial',
                'action_generate_document',
                'action_discount_stock',
                'action_mark_payment',
                'action_send_email',
                'action_notify_dispatch',
                'action_generate_remission',
                'action_free_reserved_stock',
                'action_block_returns',
                'action_void_order',
            ]);
        });
    }
}
