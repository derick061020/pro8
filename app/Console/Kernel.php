<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->command('tenant:run')
            ->everyMinute();
        // Se ejecutara por hora guardando estado de cpu y memoria (windows/linux)
        //$schedule->command('status:server')->everyMinute();
        // $schedule->command('order:payments')->everyMinute()->appendOutputTo(storage_path('logs/order_create.log'));
        $schedule->command('order:payments')->everyMinute()->sendOutputTo(storage_path('logs/order_create.log'));
        $schedule->command('tenancy:run suscription:create-orders')->dailyAt('08:00')->timezone('America/Lima')->appendOutputTo(storage_path('logs/suscription_orders.log'));
        $schedule->command('tenancy:run suscription:check-expired')->dailyAt('08:30')->timezone('America/Lima')->appendOutputTo(storage_path('logs/suscription_expired.log'));
        // $schedule->command('tenancy:run suscription:send-reminders')->everyMinute()->appendOutputTo(storage_path('logs/suscription_reminders.log'));
        $schedule->command('tenancy:run suscription:send-reminders') ->everyMinute() ->sendOutputTo(storage_path('logs/suscription_reminders.log'));
        // Limpieza de órdenes de impresión ya impresas (status=2) — pdf_b64 es pesado
        $schedule->command('tenancy:run print-orders:prune')->dailyAt('04:00')->timezone('America/Lima')->appendOutputTo(storage_path('logs/print_orders_prune.log'));
        // Llena las tablas para libro mayor - Se desactiva CMAR - buscar opcion de url
        // $schedule->command('account_ledger:fill')->hourly();
        
        //restaurar base de datos demo para restaurant
        // $schedule->command('database:restoredemo')->dailyAt('23:50');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands() {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
