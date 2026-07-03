<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega campos para la validación de impresión local a la tabla restaurant_configurations.
     *
     * - printer_public_ip   : IP pública del cliente cuando se configuró BuhoPrinter.
     *                         Se registra al hacer "Verificar y actualizar" desde el panel.
     *                         Se compara con la IP del cliente al enviar órdenes de impresión.
     * - print_local_enabled : Si está activo, solo permite enviar órdenes de impresión
     *                         cuando la IP pública del cliente coincide con printer_public_ip.
     */
    public function up(): void
    {
        Schema::table('restaurant_configurations', function (Blueprint $table) {
            $table->string('printer_public_ip', 45)->nullable()->after('printer_status');
            $table->boolean('print_local_enabled')->default(false)->after('printer_public_ip');
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_configurations', function (Blueprint $table) {
            $table->dropColumn(['printer_public_ip', 'print_local_enabled']);
        });
    }
};
