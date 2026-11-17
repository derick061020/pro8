<?php

namespace App\Console\Commands;

use Facades\App\Http\Controllers\Tenant\DocumentController;
use Illuminate\Console\Command;
use Modules\Document\Helpers\ConsultCdr;
use App\Traits\CommandTrait;
use App\Models\Tenant\{
    Configuration,
    Document
};

class SendAllSunatCommand extends Command
{
    use CommandTrait;

    /** Facturas más antiguas que esto ya no se reintentan solas */
    private const MAX_AGE_DAYS = 30;

    /** Boletas: SUNAT las recibe hasta 7 días después de emitidas */
    private const MAX_AGE_DAYS_BOLETAS = 7;

    /** Máximo por empresa y por ejecución */
    private const BATCH = 100;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'online:send-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all pending documents to be sent to the Sunat';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        if (Configuration::firstOrFail()->cron) {
            if ($this->isOffline()) {
                $this->info('Offline service is enabled');

                return;
            }

            // Pendientes de envío: registrados (01) o enviados sin respuesta (03).
            // No se filtra por success_sunat_shipping_status: antes un envío que
            // fallaba quedaba marcado como hecho y no se volvía a intentar nunca.
            // Boletas (grupo 02) también van una por una: así las ha venido enviando
            // el hotel (aceptadas sin resumen diario). Antes solo entraban facturas
            // y una boleta que no salía al emitirse se quedaba pendiente hasta que
            // alguien la mandaba a mano. Las boletas solo dentro del plazo de SUNAT.
            $documents = Document::query()
                ->where(function ($q) {
                    $q->where(function ($f) {
                        $f->where('group_id', '01')
                          ->where('date_of_issue', '>=', now()->subDays(self::MAX_AGE_DAYS)->format('Y-m-d'));
                    })->orWhere(function ($b) {
                        $b->where('group_id', '02')
                          ->where('date_of_issue', '>=', now()->subDays(self::MAX_AGE_DAYS_BOLETAS)->format('Y-m-d'));
                    });
                })
                ->where('send_server', 0)
                ->whereIn('state_type_id', ['01', '03'])
                ->orderBy('id')
                ->limit(self::BATCH)
                ->get();

            $sent = 0;
            $failed = 0;
            foreach ($documents as $document) {
                $label = "{$document->series}-{$document->number}";
                try {
                    // send() no lanza excepciones: si falla devuelve success=false
                    $response = DocumentController::send($document->id);
                    $document->refresh();

                    // Resuelto = SUNAT ya respondió: aceptado (05), observado (07) o rechazado (09)
                    $resolved = in_array($document->state_type_id, ['05', '07', '09'], true);

                    // Si no quedó resuelto puede que SUNAT ya lo tenga: el caso típico es
                    // el código 1033 "registrado previamente", que deja el comprobante
                    // como "por regularizar" y se reintentaría para siempre. Se consulta
                    // el CDR: si existe, ConsultCdr actualiza el estado (aceptado/observado).
                    if (!$resolved) {
                        try {
                            (new ConsultCdr())->search($document);
                            $document->refresh();
                            $resolved = in_array($document->state_type_id, ['05', '07', '09'], true);
                            if ($resolved) {
                                $response['message'] = 'Recuperado con la consulta de CDR: ya estaba en SUNAT';
                            }
                        } catch (\Throwable $e) {
                            // Sin CDR todavía: queda para el siguiente intento
                        }
                    }

                    $document->sunat_shipping_status = json_encode($response);
                    $document->success_sunat_shipping_status = $resolved;
                    $document->save();

                    if ($resolved) {
                        $sent++;
                        $this->info("{$label}: estado {$document->state_type_id}".(!empty($response['message']) ? " — {$response['message']}" : ''));
                    } else {
                        $failed++;
                        $this->warn("{$label}: sin respuesta de SUNAT, se reintentará — ".($response['message'] ?? ''));
                    }
                }
                catch (\Throwable $e) {
                    $failed++;
                    $document->success_sunat_shipping_status = false;
                    $document->sunat_shipping_status = json_encode([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ]);
                    $document->save();
                    $this->warn("{$label}: error, se reintentará — {$e->getMessage()}");
                }
            }

            $this->info("Comprobantes pendientes: {$documents->count()} · resueltas: {$sent} · por reintentar: {$failed}");
        }
        else {
            $this->info('The crontab is disabled');
        }

        $this->info('The command is finished');
    }
}
