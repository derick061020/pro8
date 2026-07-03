<?php

// Modules/ClaimsBook/Mail/ClaimAssigned.php

namespace Modules\ClaimsBook\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable para notificar al usuario asignado cuando se registra un nuevo reclamo.
 * Recibe un array $data con las claves: user_name, claim, status.
 */
class ClaimAssigned extends Mailable
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
        $claimType  = $this->data['claim']['claim_type'] === 'queja' ? 'Queja' : 'Reclamo';

        return $this->subject("Nuevo {$claimType} asignado: {$claimCode}")
                    ->from(config('mail.username'))
                    ->view('claimsbook::emails.claim_assigned');
    }
}
