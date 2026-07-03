<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrinterNameDocumentsToConfigurations extends Migration
{
    /**
     * Agrega el campo printer_name_documents a la tabla configurations.
     * Almacena el nombre de la impresora seleccionada para la impresión automática de documentos (auto_print).
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->string('printer_name_documents')->nullable()->after('auto_print');
        });
    }

    /**
     * Revierte el cambio.
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('printer_name_documents');
        });
    }
}
