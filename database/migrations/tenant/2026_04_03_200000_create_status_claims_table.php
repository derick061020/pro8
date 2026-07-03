<?php

// Modules/ClaimsBook — migración: tabla de estados del libro de reclamaciones

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusClaimsTable extends Migration
{
    /**
     * Crea la tabla status_claims para gestionar los estados del libro de reclamaciones.
     * Es análoga a status_orders pero con soporte explícito de estado final.
     */
    public function up()
    {
        Schema::create('status_claims', function (Blueprint $table) {
            $table->increments('id');

            // Nombre descriptivo del estado (ej: "Recibido", "En revisión", "Resuelto")
            $table->string('description', 60);

            // Color hexadecimal para representación visual en la UI
            $table->string('color', 7)->default('#909399');

            // Posición en la lista (para drag-and-drop de reordenamiento)
            $table->unsignedSmallInteger('sort_order')->default(0);

            // Indica si este es el estado con el que se registra un reclamo nuevo.
            // La unicidad de true se controla a nivel de aplicación (solo uno puede ser true).
            $table->boolean('is_initial')->default(false);

            // Indica si este estado cierra definitivamente el reclamo.
            // La unicidad de true se controla a nivel de aplicación.
            $table->boolean('is_final')->default(false);

            // Acción: enviar email de notificación al reclamante al pasar a este estado
            $table->boolean('action_send_email')->default(false);

            $table->timestamps();
        });

        // Insertar estados iniciales por defecto
        DB::table('status_claims')->insert([
            [
                'description'      => 'Registrado',
                'color'            => '#409EFF',
                'sort_order'       => 0,
                'is_initial'       => true,
                'is_final'         => false,
                'action_send_email'=> false,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'description'      => 'Leido',
                'color'            => '#67C23A',
                'sort_order'       => 1,
                'is_initial'       => false,
                'is_final'         => false,
                'action_send_email'=> false,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'description'      => 'Cerrado',
                'color'            => '#F56C6C',
                'sort_order'       => 2,
                'is_initial'       => false,
                'is_final'         => true,
                'action_send_email'=> true,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);
    }

    /**
     * Revierte la creación de la tabla status_claims.
     */
    public function down()
    {
        Schema::dropIfExists('status_claims');
    }
}
