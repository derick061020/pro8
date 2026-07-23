<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Restaurant\Models\PrintOrder;

class PrunePrintOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'print-orders:prune {--days=1 : Antigüedad mínima en días para eliminar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina órdenes de impresión ya impresas (status=2). El campo pdf_b64 es pesado, evita el crecimiento de la tabla.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $threshold = Carbon::now()->subDays($days);

        $deleted = PrintOrder::where('status', 2)
            ->where('created_at', '<', $threshold)
            ->delete();

        $this->info("PrintOrders impresas eliminadas (status=2, > {$days}d): {$deleted}");

        return self::SUCCESS;
    }
}
