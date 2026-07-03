<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega campos de configuración de impresión a la tabla restaurant_configurations.
     *
     * - printer_enabled   : activa/desactiva la integración con BuhoPrinter.
     * - printer_host      : URL del servicio BuhoPrinter en la red local
     *                       (ej. https://192.168.1.100:8080). No se expone al frontend
     *                       en texto plano dentro del payload de respuesta general.
     * - printer_status    : valor informativo que indica si el Facturador logró
     *                       contactar a BuhoPrinter la última vez que se verificó.
     *                       NO refleja el estado en tiempo real; solo sirve como
     *                       referencia de la última verificación exitosa o fallida.
     * - printer_name_comanda   : impresora asignada para impresión de comandas.
     * - printer_name_documents : impresora asignada para impresión de documentos.
     */
    public function up(): void
    {
        Schema::table('restaurant_configurations', function (Blueprint $table) {
            $table->boolean('printer_enabled')->default(false)->after('replace_template_mozo');
            $table->string('printer_host', 255)->nullable()->after('printer_enabled');
            $table->string('printer_status', 50)->nullable()->after('printer_host');
            $table->string('printer_name_comanda', 255)->nullable()->after('printer_status');
            $table->string('printer_name_documents', 255)->nullable()->after('printer_name_comanda');
            $table->string('printer_name_precuenta', 255)->nullable()->after('printer_name_documents');
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_configurations', function (Blueprint $table) {
            $table->dropColumn([
                'printer_enabled',
                'printer_host',
                'printer_status',
                'printer_name_comanda',
                'printer_name_documents',
                'printer_name_precuenta',
            ]);
        });
    }
};
