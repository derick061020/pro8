<?php
namespace Modules\Ecommerce\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $subject = isset($this->data['order']['number_document'])
            ? "Estado de su pedido " . $this->data['order']['number_document']
            : 'Estado de su pedido';

        return $this->subject($subject)
            ->view('ecommerce::mail.notify_client', $this->data);
    }
}
