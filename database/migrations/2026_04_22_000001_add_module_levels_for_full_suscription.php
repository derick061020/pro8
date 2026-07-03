<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $module = DB::connection('system')->table('modules')
            ->where('value', 'suscription_app')
            ->first();

        if (!$module) {
            return;
        }

        $levels = [
            [
                'module_id'   => $module->id,
                'value'       => 'suscription_app_pending_payments',
                'description' => 'Pagos pendientes',
            ],
            [
                'module_id'   => $module->id,
                'value'       => 'suscription_app_payment_reminders',
                'description' => 'Recordatorios de pago',
            ],
        ];

        foreach ($levels as $level) {
            $exists = DB::connection('system')->table('module_levels')
                ->where('value', $level['value'])
                ->exists();

            if (!$exists) {
                DB::connection('system')->table('module_levels')->insert($level);
            }
        }
    }

    public function down()
    {
        DB::connection('system')->table('module_levels')
            ->whereIn('value', [
                'suscription_app_pending_payments',
                'suscription_app_payment_reminders',
            ])
            ->delete();
    }
};
