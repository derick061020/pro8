<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\FullSuscription\Models\Tenant\SuscriptionOrder;

class SuscriptionMarkOrdersAsExpiredCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suscription:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar que ordenes de suscripcion se han pasado de su fecha de vencimiento';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        SuscriptionOrder::past()
            ->where('status', SuscriptionOrder::STATUS_PENDING)
            ->update(['status' => SuscriptionOrder::STATUS_EXPIRED]);

        return Command::SUCCESS;
    }
}
