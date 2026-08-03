<?php

namespace Modules\Offline\Console;

use Illuminate\Console\Command;
use Modules\Offline\Models\OfflineConfiguration;
use Modules\Offline\Services\Connectivity;
use Modules\Offline\Services\SyncClient;
use Throwable;

/**
 * Una pasada de sincronización. Sin opciones hace el ciclo completo.
 */
class OfflineSyncCommand extends Command
{
    protected $signature = 'offline:sync
                            {--push : Solo subir los cambios encolados}
                            {--documents : Solo subir los comprobantes pendientes}
                            {--pull : Solo bajar datos maestros}
                            {--ranges : Solo reponer correlativos}';

    protected $description = 'Sincroniza este terminal con el servidor online';

    public function handle(): int
    {
        $configuration = OfflineConfiguration::current();

        if (!$configuration->isClient()) {
            $this->line('Esta instalación es el servidor online: no hay nada que sincronizar.');

            return self::SUCCESS;
        }

        if (!$configuration->canSync()) {
            $this->error('El terminal no está pareado. Ejecutá primero: php artisan offline:pair');

            return self::FAILURE;
        }

        if (!Connectivity::isOnline(true)) {
            $this->warn('Sin conexión con el servidor. Los cambios quedan en cola.');

            return self::SUCCESS;
        }

        $client   = new SyncClient($configuration);
        $selected = $this->selectedSteps();

        try {
            if (in_array('documents', $selected, true)) {
                $this->report('Comprobantes', $client->pushDocuments());
            }

            if (in_array('push', $selected, true)) {
                $this->report('Subida', $client->push());
            }

            if (in_array('pull', $selected, true)) {
                $this->report('Bajada', $client->pull());
            }

            if (in_array('ranges', $selected, true)) {
                $this->report('Correlativos', $client->refillRanges());
            }
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Sin banderas se corre todo, en el orden en que conviene.
     *
     * @return array<int, string>
     */
    private function selectedSteps(): array
    {
        $steps = array_values(array_filter(
            ['documents', 'push', 'pull', 'ranges'],
            fn (string $step) => $this->option($step)
        ));

        return $steps ?: ['documents', 'push', 'pull', 'ranges'];
    }

    private function report(string $label, array $result): void
    {
        $detail = collect($result)
            ->except(['success', 'message'])
            ->map(fn ($value, $key) => "{$key}={$value}")
            ->implode(' ');

        if (!empty($result['success'])) {
            $this->info(trim("{$label}: OK {$detail}"));

            return;
        }

        $this->warn(trim("{$label}: {$detail} — " . ($result['message'] ?? 'con errores')));
    }
}
