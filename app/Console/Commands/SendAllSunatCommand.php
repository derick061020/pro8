<?php

namespace App\Console\Commands;

use Facades\App\Http\Controllers\Tenant\DocumentController;
use Illuminate\Console\Command;
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
            $documents = Document::query()
                ->where('group_id', '01')
                ->where('send_server', 0)
                ->whereIn('state_type_id', ['01', '03'])
                ->where('date_of_issue', '>=', now()->subDays(self::MAX_AGE_DAYS)->format('Y-m-d'))
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

                    $document->sunat_shipping_status = json_encode($response);
                    $document->success_sunat_shipping_status = $resolved;
                    $document->save();

                    if ($resolved) {
                        $sent++;
                        $this->info("{$label}: estado {$document->state_type_id}");
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

            $this->info("Facturas pendientes: {$documents->count()} · resueltas: {$sent} · por reintentar: {$failed}");
        }
        else {
            $this->info('The crontab is disabled');
        }

        $this->info('The command is finished');
    }
}
