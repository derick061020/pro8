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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->nullable();
            $table->boolean('required')->default(false);
            $table->json('options')->nullable();
            $table->integer('order')->default(0);

            // boolean flags por tipo de documento para búsquedas rápidas
            $table->boolean('enabled_for_documents')->default(false);
            $table->boolean('enabled_for_sale_notes')->default(false);
            $table->boolean('enabled_for_dispatches')->default(false);
            $table->boolean('enabled_for_order_notes')->default(false);

            $table->timestamps();
        });

        // Añadir columna JSON en tablas de documentos si existen
        Schema::table('documents', function (Blueprint $table) {
            $table->json('custom_fields_data')->nullable();
        });

        Schema::table('sale_notes', function (Blueprint $table) {
            $table->json('custom_fields_data')->nullable();
        });

        Schema::table('dispatches', function (Blueprint $table) {
            $table->json('custom_fields_data')->nullable();
        });

        Schema::table('order_notes', function (Blueprint $table) {
            $table->json('custom_fields_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('custom_fields_data');
        });

        Schema::table('sale_notes', function (Blueprint $table) {
            $table->dropColumn('custom_fields_data');
        });

        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropColumn('custom_fields_data');
        });

        Schema::table('order_notes', function (Blueprint $table) {
            $table->dropColumn('custom_fields_data');
        });

        Schema::dropIfExists('custom_fields');
    }
};
