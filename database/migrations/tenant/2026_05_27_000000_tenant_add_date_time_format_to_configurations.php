<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddDateTimeFormatToConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('configurations', 'date_format')) {
                $table->string('date_format')->default('DD-MM-YYYY')
                    ->comment('Plantilla de formato de fecha para mostrar en listados y formularios. Ej: DD-MM-YYYY=27-05-2026, DD/MM/YYYY=27/05/2026, YYYY-MM-DD=2026-05-27. Se guarda con la sintaxis de moment.js.');
            }
            if (!Schema::hasColumn('configurations', 'time_format')) {
                $table->string('time_format')->default('HH:mm:ss')
                    ->comment('Plantilla de formato de hora para mostrar en listados y formularios. Ej: HH:mm=14:30, HH:mm:ss=14:30:45, hh:mm A=02:30 PM. Se guarda con la sintaxis de moment.js.');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            if (Schema::hasColumn('configurations', 'date_format')) {
                $table->dropColumn('date_format');
            }
            if (Schema::hasColumn('configurations', 'time_format')) {
                $table->dropColumn('time_format');
            }
        });
    }
}
