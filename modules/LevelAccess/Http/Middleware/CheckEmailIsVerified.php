<?php

namespace Modules\LevelAccess\Http\Middleware;

use Closure;
use App\Models\Tenant\Configuration;
use App\Models\System\Client;
use App\Models\System\PaymentOrder;
use Hyn\Tenancy\Contracts\CurrentHostname;
use Illuminate\Support\Facades\Log;

class CheckEmailIsVerified
{
    public function handle($request, Closure $next)
    {
        $configuration = Configuration::getDataToCheckGuestEmail();
        if ($request->ajax()) return $next($request);


        if (!$configuration || !$configuration->from_guest_register) {
            return $next($request);
        }

        if ($configuration->applyCheckGuestEmail() && $request->user() && $request->user()->isNotVerifiedUserEmail()) {

            if ($configuration->isTestDaysExpired()) {
                $order = $this->hasPaymentOrder();

                if ($order) {
                    $uuid = $order->uuid;
                    return redirect()->route('system.payments-view.index', ['uuid' => $uuid]);
                } else {
                    return redirect()->route('tenant.not-verified-email.index');
                }
            } else {
                return redirect()->route('tenant.not-verified-email.index');
            }
        }


        return $next($request);
    }

    private function hasPaymentOrder(): PaymentOrder | null
    {
        $hostname = app(CurrentHostname::class);
        if (!$hostname) return null;

        $client = Client::where('hostname_id', $hostname->id)->first();
        if (!$client) return null;

        return PaymentOrder::where('client_id', $client->id)
            ->where('created_by', 'Autoregistro')
            ->where('order_state_id', 1)
            ->exists();
    }
}
