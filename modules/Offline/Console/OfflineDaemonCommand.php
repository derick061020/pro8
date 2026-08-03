<?php

namespace Modules\Offline\Console;

use Illuminate\Console\Command;
use Modules\Offline\Models\OfflineConfiguration;
use Modules\Offline\Services\Connectivity;
use Modules\Offline\Services\SyncClient;
use Throwable;

/**
 * Sincronización continua en segundo plano.
 *
 * Lo arranca el launcher de Windows junto con el sistema. En Windows no hay
 * cron, así que en vez de depender del programador de tareas el proceso vive
 * mientras el usuario tiene pro8 abierto.
 */
class OfflineDaemonCommand extends Command
{
    protected $signature = 'offline:daemon
                            {--interval= : Segundos entre ciclos (por defecto, el configurado)}
                            {--once : Correr un solo ciclo y salir}';

    protected $description = 'Mantiene el terminal sincronizándose con el servidor online';

    /** Espera cuando el servidor no responde, para no golpear la red en vano. */
    private const OFFLINE_BACKOFF = 30;

    public function handle(): int
    {
        $configuration = OfflineConfiguration::current();

        if (!$configuration->isClient()) {
            $this->line('Esta instalación no es un terminal offline. El demonio no arranca.');

            return self::SUCCESS;
        }

        $interval = (int)($this->option('interval') ?: $configuration->sync_interval ?: 60);

        $this->info("Demonio de sincronización activo (cada {$interval}s). Ctrl+C para detener.");

        do {
            $this->cycle();

            if ($this->option('once')) {
                break;
            }

            sleep(Connectivity::isOnline() ? $interval : self::OFFLINE_BACKOFF);
        } while (true);

        return self::SUCCESS;
    }

    /**
     * Un ciclo nunca debe tumbar el demonio: si algo falla se registra y se
     * vuelve a intentar en la vuelta siguiente.
     */
    private function cycle(): void
    {
        try {
            $configuration = OfflineConfiguration::current();

            if (!$configuration->canSync()) {
                return;
            }

            $summary = (new SyncClient($configuration))->synchronize();

            if (empty($summary['online'])) {
                return;
            }

            $sent = ($summary['push']['sent'] ?? 0) + ($summary['documents']['sent'] ?? 0);

            if ($sent > 0) {
                $this->line('[' . now()->toTimeString() . "] Se sincronizaron {$sent} registros.");
            }
        } catch (Throwable $e) {
            $this->error('[' . now()->toTimeString() . '] ' . $e->getMessage());
            report($e);
        }
    }
}
