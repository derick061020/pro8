<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $module = DB::connection('tenant')->table('modules')
            ->where('value', 'suscription_app')
            ->first();

        if (!$module) {
            return;
        }

        $levels = [
            [
                'module_id'   => $module->id,
                'description' => 'Pagos pendientes',
                'value'       => 'suscription_app_pending_payments',
            ],
            [
                'module_id'   => $module->id,
                'description' => 'Recordatorios de pago',
                'value'       => 'suscription_app_payment_reminders',
            ],
        ];

        foreach ($levels as $level) {
            $exists = DB::connection('tenant')
                ->table('module_levels')
                ->where('value', $level['value'])
                ->exists();

            if (!$exists) {
                DB::connection('tenant')->table('module_levels')->insert($level);
            }
        }
    }

    public function down()
    {
        DB::connection('tenant')->table('module_levels')
            ->whereIn('value', [
                'suscription_app_pending_payments',
                'suscription_app_payment_reminders',
            ])->delete();
    }
};
