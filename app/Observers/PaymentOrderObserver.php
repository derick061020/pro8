<?php

namespace App\Observers;

use App\Models\System\PaymentOrder;
use App\Models\Tenant\Configuration as TenantConfiguration;
use Hyn\Tenancy\Environment;
use Illuminate\Support\Carbon;

class PaymentOrderObserver
{
    public function updated(PaymentOrder $order): void
    {
        $becamePaid = (int) $order->order_state_id === 2
            && (int) $order->getOriginal('order_state_id') !== 2;

        if (!$becamePaid) {
            return;
        }

        if (!$order->plan_id || !$order->client) {
            return;
        }

        $this->applyPlanChange($order);
    }

    private function applyPlanChange(PaymentOrder $order): void
    {
        $client = $order->client;
        $plan = $order->plan;

        if (!$plan) {
            return;
        }

        $client->update([
            'plan_id' => $plan->id,
            'ending_billing_cycle' => Carbon::now()->addMonth()->toDateString(),
        ]);

        if (!$client->hostname || !$client->hostname->website) {
            return;
        }

        $tenancy = app(Environment::class);
        $tenancy->tenant($client->hostname->website);

        $tenantConfig = TenantConfiguration::first();
        if ($tenantConfig) {
            $tenantConfig->plan = $plan;
            $tenantConfig->save();
        }
    }
}
