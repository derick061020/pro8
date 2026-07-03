<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('cuenta_compra')->nullable();
            $table->string('cuenta_venta')->nullable();
            $table->string('cuenta_compra_devolucion')->nullable();
            $table->string('cuenta_venta_devolucion')->nullable();
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'cuenta_compra',
                'cuenta_venta',
                'cuenta_compra_devolucion',
                'cuenta_venta_devolucion',
            ]);
        });
    }
};
