<?php

// Modules/ClaimsBook/Mail/ClaimStatusChanged.php

namespace Modules\ClaimsBook\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable para notificar al reclamante cuando cambia el estado de su reclamo.
 * Recibe un array $data con las claves: claim, status, resolution y route_list.
 */
class ClaimStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Construye el mensaje: asunto, remitente y vista blade.
     */
    public function build(): static
    {
        $claimCode  = $this->data['claim']['code'] ?? '';
        $statusName = $this->data['status']['name'] ?? '';

        return $this->subject("Estado de tu reclamo {$claimCode} : {$statusName}")
                    ->from(config('mail.username'))
                    ->view('claimsbook::emails.claim_status_changed');
    }
}
