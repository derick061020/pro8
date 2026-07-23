<?php

namespace App\Observers;

use App\Models\Tenant\Purchase;
use Modules\Webhook\Services\WebhookDispatcher;
use Modules\Webhook\Services\WebhookEvents;

class PurchaseObserver
{
    /**
     * Handle the purchase "created" event.
     *
     * @param  \App\Models\Tenant\Purchase  $purchase
     * @return void
     */
    public function created(Purchase $purchase)
    {
        app(WebhookDispatcher::class)->dispatch(WebhookEvents::PURCHASE_CREATED, $purchase);
    }
}
