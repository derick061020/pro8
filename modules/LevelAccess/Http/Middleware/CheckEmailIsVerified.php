<?php

namespace Modules\LevelAccess\Http\Middleware;

use Closure;
use App\Models\Tenant\Configuration;
use App\Models\System\Client;
use App\Models\System\PaymentOrder;
use Hyn\Tenancy\Contracts\CurrentHostname;


class CheckEmailIsVerified
{
    public function handle($request, Closure $next)
    {
        if ($request->ajax()) return $next($request);

        $configuration = Configuration::getDataToCheckGuestEmail();

        if (!$configuration || !$configuration->from_guest_register) {
            return $next($request);
        }

        if ($configuration->applyCheckGuestEmail() && $request->user() && $request->user()->isNotVerifiedUserEmail()) {
            return redirect()->route('tenant.not-verified-email.index');
        }

        if ($this->hasPendingGuestPayment()) {
            return redirect()->route('tenant.pending-payment.index');
        }

        return $next($request);
    }

    private function hasPendingGuestPayment()
    {
        $hostname = app(CurrentHostname::class);
        if (!$hostname) return false;

        $client = Client::where('hostname_id', $hostname->id)->first();
        if (!$client) return false;

        return PaymentOrder::where('client_id', $client->id)
            ->where('created_by', 'Autoregistro')
            ->where('order_state_id', 1)
            ->exists();
    }
}
