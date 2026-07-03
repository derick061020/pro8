<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\FullSuscription\Models\Tenant\SuscriptionOrder;
use Modules\FullSuscription\Models\Tenant\SuscriptionPaymentReminder;

class SuscriptionSendRemindersCommand extends Command
{

    const MEDIUM_WS = 'whatsapp';
    const MEDIUM_EMAIL = 'email';

    const TYPE_BEFORE = 'before';
    const TYPE_SAME_DAY = 'same_day';
    const TYPE_AFTER = 'after';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suscription:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta toda los recordatorios de pago de afiliaciones programados para el día';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $reminders = SuscriptionPaymentReminder::all();


        if ($reminders->count() > 0) {
            foreach ($reminders as $reminder) {
                $medium = $reminder->shipping_medium;
                $type = $reminder->reminder_type;
                $days = $reminder->reminder_days;
                $time = $reminder->reminder_time;

                $now = now();
                if ($now->hour == $time->hour && $now->minute == $time->minute) {
                    $orders = SuscriptionOrder::whereIn('status', ['pending', 'rejected', 'expired']);

                    if ($type == self::TYPE_BEFORE) {
                        $orders->whereDate('date_of_due', Carbon::now()->addDays($days));
                    } else if ($type == self::TYPE_SAME_DAY) {
                        $orders->whereDate('date_of_due', Carbon::now());
                    } else if ($type == self::TYPE_AFTER) {
                        $orders->whereDate('date_of_due', Carbon::now()->subDays($days));
                    }
                    $orders = $orders->get();

                    foreach ($orders as $order) {
                        $order->notification([$medium]);
                    }
                }

            }
        }

        return Command::SUCCESS;
    }
}
