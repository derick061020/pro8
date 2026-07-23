<?php

namespace App\Http\ViewComposers\Tenant;

use App\Models\Tenant\Company;
use App\Models\Tenant\Order;
use App\Models\Tenant\StatusOrder;

class CompanyViewComposer
{
    public function compose($view)
    {
        $view->vc_company = Company::first();

        $initialPayment = StatusOrder::where('is_payment_status', true)->where('is_initial', true)->orderBy('sort_order')->first()
            ?: StatusOrder::where('is_payment_status', true)->orderBy('sort_order')->first();

        $view->vc_orders = $initialPayment
            ? Order::where('payment_status_order_id', $initialPayment->id)->count()
            : 0;
    }
}
