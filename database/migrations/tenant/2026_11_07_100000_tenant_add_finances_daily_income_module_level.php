<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Permiso "Ingresos diarios" (Finanzas) para activar o quitar por usuario el
 * reporte de ingresos diarios del hotel. Antes era sólo para el tipo
 * administrador: se asigna a los administradores para que nadie pierda acceso.
 */
return new class extends Migration
{
    public function up()
    {
        $db = DB::connection('tenant');
        $module = $db->table('modules')->where('value', 'finance')->first();
        if (!$module) {
            return;
        }

        $levelId = $db->table('module_levels')->where('value', 'finances_daily_income')->value('id');
        if (!$levelId) {
            $levelId = $db->table('module_levels')->insertGetId([
                'module_id' => $module->id,
                'value' => 'finances_daily_income',
                'description' => 'Ingresos diarios',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $adminIds = $db->table('users')->where('type', 'admin')->pluck('id');
        foreach ($adminIds as $userId) {
            $assigned = $db->table('module_level_user')
                ->where('user_id', $userId)->where('module_level_id', $levelId)->exists();
            if (!$assigned) {
                $db->table('module_level_user')->insert(['user_id' => $userId, 'module_level_id' => $levelId]);
            }
        }
    }

    public function down()
    {
        $db = DB::connection('tenant');
        $levelId = $db->table('module_levels')->where('value', 'finances_daily_income')->value('id');
        if ($levelId) {
            $db->table('module_level_user')->where('module_level_id', $levelId)->delete();
            $db->table('module_levels')->where('id', $levelId)->delete();
        }
    }
};
