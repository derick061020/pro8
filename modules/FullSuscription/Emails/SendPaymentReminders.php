<?php

namespace Modules\FullSuscription\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\FullSuscription\Models\Tenant\SuscriptionOrder;

class SendPaymentReminders extends Mailable
{
    use Queueable, SerializesModels;

    public SuscriptionOrder $order;
    public string $message;

    public function __construct(SuscriptionOrder $order, string $message)
    {
        $this->order   = $order;
        $this->message = $message;
    }

    public function build()
    {
        return $this->view('full_suscription::email.payment-reminders', [
                        'content' => $this->message,
                    ])
                    ->subject("Recordatorio de pago pendiente - Orden #{$this->order->number}")
                    ->from(config('mail.username'));
    }
}
