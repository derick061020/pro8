<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Añade los campos enable_electronic_documents y enable_store_pickup a configuration_ecommerce,
 * y crea la tabla ecommerce_pickup_branches para gestionar sucursales de recojo en tienda.
 */
class AddEnableElectronicDocumentsToConfigurationEcommerce extends Migration
{
    public function up()
    {
        // Columnas en configuration_ecommerce
        if (Schema::hasTable('configuration_ecommerce')) {
            Schema::table('configuration_ecommerce', function (Blueprint $table) {
                $table->boolean('enable_electronic_documents')->default(false)->after('delivery_no_coverage_message');
                $table->boolean('enable_store_pickup')->default(false)->after('enable_electronic_documents');
            });
        }

        // Tabla de sucursales de recojo en tienda
        if (!Schema::hasTable('ecommerce_pickup_branches')) {
            Schema::create('ecommerce_pickup_branches', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('address')->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        // Eliminar tabla de sucursales
        Schema::dropIfExists('ecommerce_pickup_branches');

        // Revertir columnas
        if (Schema::hasTable('configuration_ecommerce')) {
            Schema::table('configuration_ecommerce', function (Blueprint $table) {
                if (Schema::hasColumn('configuration_ecommerce', 'enable_store_pickup')) {
                    $table->dropColumn('enable_store_pickup');
                }
                if (Schema::hasColumn('configuration_ecommerce', 'enable_electronic_documents')) {
                    $table->dropColumn('enable_electronic_documents');
                }
            });
        }
    }
}
