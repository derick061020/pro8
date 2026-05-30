<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('print_orders', function (Blueprint $table) {
            $table->id();
            $table->string('name_printer')->comment('Nombre de la impresora');
            $table->tinyInteger('status')->default(0)->comment('Estado de la orden de impresión: 0 = pendiente, 1 = procesando, 2 = impresa');
            $table->longText('pdf_b64')->comment('Archivo PDF codificado en base64');
            $table->timestamps();
        });

        Schema::table('restaurant_configurations', function (Blueprint $table) {
            $table->boolean('enabled_server_print')->default(false)->comment('Habilita el stream de impresión por SSE');
            $table->boolean('replace_template_mozo')->default(false)->comment('Habilita la impresión de la plantilla del facturador en lugar de la de mozo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('print_orders');

        Schema::table('restaurant_configurations', function (Blueprint $table) {
            $table->dropColumn('enabled_server_print');
            $table->dropColumn('replace_template_mozo');
        });
    }
};
