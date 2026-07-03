<?php

namespace Modules\FullSuscription\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\FullSuscription\Models\Tenant\SuscriptionOrder;

class SendLinkSubscriptionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public SuscriptionOrder $order;

    public function __construct(SuscriptionOrder $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        $customer = $this->order->suscription->parent_customer;
        $name = $customer['name'] ?? 'Cliente';

        return $this->view('fullsuscription::email.email')
                    ->subject("Enlace de suscripción para {$name}")
                    ->from(config('mail.username'));
    }
}
