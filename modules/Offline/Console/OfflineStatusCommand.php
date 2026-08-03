<?php

namespace Modules\Offline\Console;

use Illuminate\Console\Command;
use Modules\Offline\Models\OfflineConfiguration;
use Modules\Offline\Services\StatusSnapshot;

/**
 * Estado del terminal.
 *
 * La salida `--json` la consume el launcher de Windows para el globo de la
 * bandeja del sistema, así que el formato es contrato: si cambia, hay que
 * actualizar desktop/launcher/main.go.
 */
class OfflineStatusCommand extends Command
{
    protected $signature = 'offline:status {--json : Devolver el estado en JSON}';

    protected $description = 'Muestra el estado de sincronización del terminal offline';

    public function handle(): int
    {
        $status = StatusSnapshot::build();

        // Deja la foto actualizada también cuando se consulta a mano: si el
        // demonio no está corriendo, el launcher igual ve algo reciente.
        StatusSnapshot::write($status);

        if ($this->option('json')) {
            $this->line(json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }

        $this->renderTable($status);

        return self::SUCCESS;
    }

    private function renderTable(array $status): void
    {
        $this->newLine();
        $this->line('<options=bold>Estado del terminal offline</>');

        $this->table([], [
            ['Modo', $status['mode'] === OfflineConfiguration::MODE_CLIENT ? 'Terminal (cliente)' : 'Servidor online'],
            ['Terminal', $status['terminal_code'] ?: '—'],
            ['Servidor', $status['server'] ?: '—'],
            ['Pareado', $status['paired'] ? 'sí' : 'no'],
            ['Conexión', $status['online'] ? 'en línea' : 'sin conexión'],
            ['Última subida', $status['last_push_at'] ?: 'nunca'],
            ['Última bajada', $status['last_pull_at'] ?: 'nunca'],
        ]);

        if (!isset($status['pending'])) {
            return;
        }

        $this->newLine();
        $this->line('<options=bold>Pendientes</>');
        $this->table([], [
            ['Cambios en cola', $status['pending']],
            ['Comprobantes sin subir', $status['pending_documents']],
            ['Trabados (requieren revisión)', $status['stuck']],
        ]);

        if (empty($status['ranges'])) {
            $this->newLine();
            $this->warn('Este terminal no tiene numeración reservada: no podrá emitir comprobantes sin internet.');

            return;
        }

        $this->newLine();
        $this->line('<options=bold>Numeración reservada</>');
        $this->table(
            ['Serie', 'Desde', 'Hasta', 'Último usado', 'Quedan'],
            array_map(fn (array $range) => [
                $range['series'],
                $range['from_number'],
                $range['to_number'],
                $range['current_number'] ?? '—',
                $range['warning'] ? "<fg=red>{$range['remaining']}</>" : $range['remaining'],
            ], $status['ranges'])
        );

        foreach ($status['ranges'] as $range) {
            if ($range['warning']) {
                $this->warn("La serie {$range['series']} está por agotarse. Conectá el equipo a internet para reponerla.");
            }
        }
    }
}
