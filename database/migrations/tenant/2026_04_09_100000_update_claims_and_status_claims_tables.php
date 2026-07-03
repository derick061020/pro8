<?php

// ClaimsBook — migración: nuevos campos en claims y status_claims

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

class UpdateClaimsAndStatusClaimsTables extends Migration
{
    /**
     * Aplica los siguientes cambios:
     *
     * claims:
     *   - assigned_user_id    FK nullable a users (responsable asignado)
     *   - attachments         JSON nullable — reemplaza attachment (migra datos)
     *   - attachment          DROP (migrado a attachments)
     *   - pdf_path            ruta al PDF de constancia generado para el cliente
     *   - public_code         código público único de 15 caracteres (NOT NULL + UNIQUE)
     *                         se asigna código aleatorio a registros existentes
     *   - response_attachments JSON nullable — archivos adjuntos a la respuesta oficial
     *   - due_date            fecha límite de atención (calculada al crear; null en histórico)
     *
     * status_claims:
     *   - assigned_user_id   FK nullable a users (para futuras notificaciones)
     *   - Cerrado pasa de sort_order 2 → 3
     *   - Se inserta "Borrador" en sort_order 2
     */
    public function up()
    {
        // ── 1. Nuevas columnas en claims ──────────────────────────────────────
        Schema::table('claims', function (Blueprint $table) {
            // Usuario asignado para atender el reclamo
            $table->unsignedInteger('assigned_user_id')
                ->nullable()
                ->after('status_claim_id');

            // Reemplaza attachment singular; acepta hasta 5 archivos como array JSON
            $table->json('attachments')->nullable()->after('channel');

            // Ruta al PDF de constancia generado para enviar al cliente
            $table->string('pdf_path', 500)->nullable()->after('attachments');

            // Código público de consulta — se llena en el backfill de abajo
            // Se declara nullable temporalmente para poder hacer el backfill
            $table->string('public_code', 15)->nullable()->after('code');

            // Archivos adjuntos asociados a la respuesta oficial del reclamo
            $table->json('response_attachments')->nullable()->after('resolution');

            // Fecha límite de atención: 15 días hábiles desde la creación
            // Null en registros históricos; se calcula automáticamente en creación nueva
            $table->date('due_date')->nullable()->after('is_closed');

            // FK al usuario asignado
            $table->foreign('assigned_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        // ── 1a. Migrar attachment → attachments ───────────────────────────────
        DB::statement("
            UPDATE claims
            SET attachments = JSON_ARRAY(attachment)
            WHERE attachment IS NOT NULL AND attachment != ''
        ");

        // ── 1b. Backfill de public_code en registros existentes ───────────────
        $ids = DB::table('claims')->pluck('id');
        foreach ($ids as $id) {
            do {
                $code = strtoupper(Str::random(15));
            } while (DB::table('claims')->where('public_code', $code)->exists());

            DB::table('claims')->where('id', $id)->update(['public_code' => $code]);
        }

        // ── 1c. Imponer NOT NULL + UNIQUE sobre public_code ───────────────────
        // Se usa DDL directo para evitar dependencia de doctrine/dbal
        DB::statement('ALTER TABLE claims MODIFY public_code VARCHAR(15) NOT NULL');
        DB::statement('ALTER TABLE claims ADD UNIQUE KEY claims_public_code_unique (public_code)');

        // ── 1d. Eliminar columna attachment ya migrada ────────────────────────
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });

        // ── 2. Nuevas columnas en status_claims ───────────────────────────────
        Schema::table('status_claims', function (Blueprint $table) {
            // Usuario responsable vinculado al estado (para notificaciones futuras)
            $table->unsignedInteger('assigned_user_id')
                ->nullable()
                ->after('action_send_email');
        });

        // Mover "Cerrado" a sort_order 3 para dejar hueco al nuevo estado
        DB::table('status_claims')
            ->where('description', 'Cerrado')
            ->update(['sort_order' => 3]);

        // Insertar estado "Borrador" — permite guardar respuesta antes de cerrar
        DB::table('status_claims')->insert([
            'description'       => 'Borrador',
            'color'             => '#E6A23C',
            'sort_order'        => 2,
            'is_initial'        => false,
            'is_final'          => false,
            'action_send_email' => false,
            'assigned_user_id'  => null,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }

    /**
     * Revierte todos los cambios aplicados en up().
     */
    public function down()
    {
        // ── Revertir status_claims ────────────────────────────────────────────
        DB::table('status_claims')->where('description', 'Borrador')->delete();
        DB::table('status_claims')
            ->where('description', 'Cerrado')
            ->update(['sort_order' => 2]);

        Schema::table('status_claims', function (Blueprint $table) {
            $table->dropColumn('assigned_user_id');
        });

        // ── Revertir claims ───────────────────────────────────────────────────
        // Restaurar columna attachment antes de eliminar attachments
        Schema::table('claims', function (Blueprint $table) {
            $table->string('attachment', 500)->nullable()->after('channel');
        });

        // Restaurar primer elemento de attachments → attachment
        DB::statement("
            UPDATE claims
            SET attachment = JSON_UNQUOTE(JSON_EXTRACT(attachments, '$[0]'))
            WHERE attachments IS NOT NULL
        ");

        // Eliminar índice único antes de cambiar la columna
        DB::statement('ALTER TABLE claims DROP INDEX claims_public_code_unique');

        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign(['assigned_user_id']);
            $table->dropColumn([
                'assigned_user_id',
                'attachments',
                'pdf_path',
                'public_code',
                'response_attachments',
                'due_date',
            ]);
        });
    }
}
