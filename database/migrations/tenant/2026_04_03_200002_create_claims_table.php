<?php

// Modules/ClaimsBook — migración: tabla principal de reclamos y quejas

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClaimsTable extends Migration
{
    /**
     * Crea la tabla claims que almacena los reclamos y quejas del libro de reclamaciones.
     * Estructura dividida en: identificación, datos del reclamante, producto/servicio,
     * detalles del caso y estado del proceso.
     */
    public function up()
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->increments('id');

            // ──────────────────────────────────────────────────────
            // Identificación y trazabilidad
            // ──────────────────────────────────────────────────────

            // Código único generado: {tipo}{MM}{YY}-{correlativo 4d}-{tracking 2d}
            // Ej: Q032600-0001-00 (queja), R032600-0001-00 (reclamo)
            $table->string('code', 20)->unique();

            // Número de seguimiento dentro de una cadena de reclamos relacionados
            $table->unsignedSmallInteger('tracking_number')->default(0);

            // Código base sin tracking (ej: Q032600-0001) para agrupar reclamos relacionados
            $table->string('parent_code', 15)->nullable()->index();

            // ──────────────────────────────────────────────────────
            // Paso 1: Datos del reclamante
            // ──────────────────────────────────────────────────────

            // Tipo de documento de identidad (DNI, RUC, CE, Pasaporte, etc.)
            $table->string('identity_document_type', 10);

            $table->string('identity_document_number', 20);

            // Nombre completo o razón social del reclamante
            $table->string('name', 200);

            // Ubigeo del distrito del reclamante (char(6) compatible con la tabla districts)
            $table->char('district_id', 6)->nullable();

            $table->string('address', 300)->nullable();

            $table->string('email', 150);

            $table->string('phone', 20)->nullable();

            // ──────────────────────────────────────────────────────
            // Paso 2: Producto o servicio
            // ──────────────────────────────────────────────────────

            // Tipo de bien reclamado: 'producto' o 'servicio'
            $table->string('asset_type', 15);

            $table->string('asset_description', 500);

            // Fecha de adquisición del producto o servicio
            $table->date('asset_date');

            // Indica si el reclamante tiene comprobante de pago asociado
            $table->boolean('has_receipt')->default(false);

            // Datos del comprobante de pago (opcionales según has_receipt)
            $table->string('receipt_series', 10)->nullable();
            $table->string('receipt_number', 20)->nullable();
            $table->decimal('receipt_amount', 10, 2)->nullable();
            $table->string('receipt_currency', 3)->nullable(); // PEN o USD

            // ──────────────────────────────────────────────────────
            // Paso 3: Detalles del caso
            // ──────────────────────────────────────────────────────

            // Tipo de caso: 'queja' o 'reclamo'
            $table->string('claim_type', 10);

            // Descripción detallada del caso expuesta por el reclamante
            $table->text('detail');

            // Qué espera el reclamante como resolución
            $table->text('expected_result');

            // Canal de recepción almacenado como string para preservar histórico
            // (no FK, por eso se guarda el nombre directamente)
            $table->string('channel', 100)->nullable();

            // Ruta relativa al archivo adjunto (jpg, jpeg, png, pdf, máx 1MB)
            $table->string('attachment', 500)->nullable();

            // Confirmación de veracidad de los datos por parte del reclamante
            $table->boolean('terms_accepted')->default(false);

            // ──────────────────────────────────────────────────────
            // Gestión interna (backend)
            // ──────────────────────────────────────────────────────

            // Estado actual del reclamo
            $table->unsignedInteger('status_claim_id')->nullable();

            // Resolución o respuesta oficial al reclamo (requerida al cerrar)
            $table->text('resolution')->nullable();

            // Indica si el reclamo fue cerrado definitivamente
            $table->boolean('is_closed')->default(false);

            $table->timestamps();

            // FK al estado del reclamo
            $table->foreign('status_claim_id')
                ->references('id')
                ->on('status_claims')
                ->onDelete('set null');

            // FK al distrito (ubigeo)
            $table->foreign('district_id')
                ->references('id')
                ->on('districts');
        });
    }

    /**
     * Revierte la creación de la tabla claims.
     */
    public function down()
    {
        Schema::dropIfExists('claims');
    }
}
