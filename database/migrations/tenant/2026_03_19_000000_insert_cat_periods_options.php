<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsertCatPeriodsOptions extends Migration
{
    /**
     * inserta los periodos faltantes en `cat_periods`.
     */
    public function up()
    {
        // Insertar solo los periodos faltantes como códigos de letra,
        // sin actualizar los dos existentes (M, Y).
        $records = [
            ['period' => 'D', 'name' => 'Diario', 'active' => 1],
            ['period' => 'W', 'name' => 'Semanal', 'active' => 1],
            ['period' => 'Q', 'name' => 'Quincenal', 'active' => 1],
            ['period' => 'B', 'name' => 'Bimestral', 'active' => 1],
            ['period' => 'T', 'name' => 'Trimestral', 'active' => 1],
            ['period' => 'S', 'name' => 'Semestral', 'active' => 1],
        ];

        // Usar insertOrIgnore para no lanzar error si alguno ya existe.
        DB::table('cat_periods')->insertOrIgnore($records);
    }

    public function down(){}
}
