<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Bandeja de pagos recibidos (Yape / Plin) capturados por el equipo-oido.
 *
 * El aislamiento por negocio lo da la base de datos del tenant, por eso no se
 * incluye una columna tenant_id. Las columnas matched_document_id,
 * matched_document_payment_id y claimed_by_user_id se mantienen desacopladas
 * (sin foreign key) para no acoplar esta bandeja al ciclo de vida de los
 * comprobantes ni de sus pagos.
 */
class CreateReceivedPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('received_payments', function (Blueprint $table) {
            $table->increments('id');

            // Origen del pago capturado
            $table->string('provider')->index();                 // YAPE | PLIN
            $table->decimal('amount', 10, 2);
            $table->string('sender_name')->nullable();
            $table->text('raw_text');                            // notificacion original
            $table->string('package_name');                     // app que origino la notificacion
            $table->dateTime('posted_at');                      // fecha/hora del pago capturado
            $table->string('device_id')->index();               // equipo-oido que lo capturo

            // Idempotencia del push: un mismo pago no se duplica
            $table->string('dedup_hash')->unique();

            // Estado dentro de la bandeja
            $table->string('status')->default('pending')->index(); // pending | matched | discarded

            // Vinculo (logico, sin FK) con el comprobante y su pago una vez reclamado
            $table->unsignedInteger('matched_document_id')->nullable()->index();
            $table->unsignedInteger('matched_document_payment_id')->nullable()->index();
            $table->unsignedInteger('claimed_by_user_id')->nullable()->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('received_payments');
    }
}
