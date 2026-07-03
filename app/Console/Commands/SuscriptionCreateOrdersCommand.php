<?php

namespace App\Console\Commands;

use App\Models\Tenant\Configuration;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\FullSuscription\Models\Tenant\UserRelSuscriptionPlan;

class SuscriptionCreateOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suscription:create-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Ordenes de las suscripciones de las suscripciones activas respecto a su frecuencia de cobro';

    /**
     * Execute the console command.
     * Campos importantes 
     * - periodo (lapsos de tiempo para crear la orden)
     * - fecha de emision
     * - cantidad de suscripcion a crear (si no es ilimitado)
     * @return int
     */
    public function handle()
    {
        $suscriptions = UserRelSuscriptionPlan::whereActive();


        foreach ($suscriptions as $suscription) {
            $before_day_creation = Configuration::select('before_day_creation_suscription_order')->first()->before_day_creation_suscription_order;
            $plan = $suscription->suscription_plan;
            $order_creattion_date = $suscription->orderCreationDate();
            $count = $suscription->orders_created;
            $quantity_period = $suscription->quantity_period;


            if ($order_creattion_date->subDays($before_day_creation)->isToday()) {
                if ($plan->unlimited) {
                    $suscription->createOrder();
                } else if (($count < $quantity_period)) {
                    $suscription->createOrder();
                }
            }

            if ($count === $quantity_period) {
                // Si se ha alcanzado la cantidad de periodos, se marca la afiliación como finalizada
                $suscription->update([
                    'status' => 'finished'
                ]);
            }
        }

        return Command::SUCCESS;
    }

}
