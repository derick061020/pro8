<?php

// Modules/ClaimsBook/Jobs/SendClaimStatusEmail.php

namespace Modules\ClaimsBook\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\Tenant\Configuration;
use Modules\ClaimsBook\Mail\ClaimStatusChanged;
use Modules\ClaimsBook\Models\Tenant\Claim;
use Modules\ClaimsBook\Models\Tenant\StatusClaim;

/**
 * Job para notificar al reclamante por correo cuando cambia el estado de su reclamo.
 * Sigue el mismo patrón que SendOrderStatusEmail de Modules\Ecommerce.
 */
class SendClaimStatusEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de reintentos ante fallo.
     */
    public $tries = 2;

    protected int $claimId;
    protected int $statusClaimId;
    protected string $routeList;

    public function __construct(int $claimId, int $statusClaimId, string $routeList)
    {
        $this->claimId       = $claimId;
        $this->statusClaimId = $statusClaimId;
        $this->routeList     = $routeList;
    }

    /**
     * Ejecuta la notificación de correo al reclamante.
     * Aplica la configuración SMTP del tenant antes de enviar.
     */
    public function handle(): void
    {
        // Aplicar configuración SMTP del tenant activo
        Configuration::setConfigSmtpMail();

        $claim  = Claim::find($this->claimId);
        $status = StatusClaim::find($this->statusClaimId);

        if (! $claim) {
            Log::warning("SendClaimStatusEmail: Claim {$this->claimId} not found");
            return;
        }

        if (empty($claim->email)) {
            Log::info("SendClaimStatusEmail: No email for claim {$this->claimId}");
            return;
        }

        $data = [
            'claim' => [
                'id'         => $claim->id,
                'code'       => $claim->code,
                'public_code'=> $claim->public_code,
                'claim_type' => $claim->claim_type,
                'name'       => $claim->name,
                'email'      => $claim->email,
                'detail'     => $claim->detail,
                'created_at' => $claim->created_at?->format('Y-m-d H:i'),
                'updated_at' => $claim->updated_at?->format('Y-m-d H:i'),
            ],
            'status' => [
                'id'    => $status ? $status->id          : $this->statusClaimId,
                'name'  => $status ? $status->description : null,
                'color' => $status ? ($status->color ?? '#066FD1') : '#066FD1',
            ],
            'resolution' => $claim->resolution,
            'route_list' => $this->routeList,
        ];

        $mailable = new ClaimStatusChanged($data);

        // Adjuntar el PDF de constancia si está disponible en el disco del tenant
        if ($claim->pdf_path) {
            $absolutePath = Storage::disk('tenant')->path($claim->pdf_path);
            if (file_exists($absolutePath)) {
                $mailable->attach($absolutePath, [
                    'as'   => $claim->public_code . '.pdf',
                    'mime' => 'application/pdf',
                ]);
            }
        }

        Mail::to($claim->email)->send($mailable);

        Log::info("SendClaimStatusEmail: Email enviado para reclamo {$claim->code} → estado {$status?->description}");
    }
}
