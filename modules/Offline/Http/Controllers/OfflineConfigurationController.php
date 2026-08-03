<?php

namespace Modules\Offline\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Offline\Http\Requests\OfflineConfigurationRequest;
use Modules\Offline\Http\Resources\OfflineConfigurationResource;
use Modules\Offline\Models\OfflineConfiguration;
use Modules\Offline\Models\SyncLog;
use Modules\Offline\Models\SyncQueueItem;
use Modules\Offline\Models\Terminal;
use Modules\Offline\Services\Connectivity;
use Modules\Offline\Services\NumberRangeService;
use Modules\Offline\Services\SyncClient;
use Modules\Offline\Services\SyncQueue;
use Throwable;

/**
 * Panel del modo offline.
 *
 * En un terminal muestra el estado de la sincronización y permite forzarla.
 * En el servidor online lista los terminales dados de alta.
 */
class OfflineConfigurationController extends Controller
{
    public function index()
    {
        return view('offline::offline_configurations.index');
    }

    public function record()
    {
        return new OfflineConfigurationResource(OfflineConfiguration::current());
    }

    public function store(OfflineConfigurationRequest $request)
    {
        $configuration = OfflineConfiguration::current();

        $configuration->fill($request->only([
            'is_client',
            'terminal_code',
            'terminal_name',
            'token_server',
            'url_server',
            'sync_enabled',
            'sync_interval',
            'git_branch',
        ]));

        // is_client y mode describen lo mismo; se mantienen alineados para no
        // romper el código viejo que sigue leyendo is_client.
        $configuration->mode = $configuration->is_client
            ? OfflineConfiguration::MODE_CLIENT
            : OfflineConfiguration::MODE_SERVER;

        $configuration->save();

        return [
            'success' => true,
            'message' => 'Configuración offline actualizada',
        ];
    }

    /**
     * Estado en vivo del terminal, para el panel y el indicador de conexión.
     */
    public function status()
    {
        $configuration = OfflineConfiguration::current();

        $data = [
            'mode'          => $configuration->mode,
            'is_client'     => $configuration->isClient(),
            'paired'        => $configuration->canSync(),
            'terminal_code' => $configuration->terminal_code,
            'terminal_name' => $configuration->terminal_name,
            'server'        => $configuration->serverUrl(),
            'online'        => $configuration->canSync() ? Connectivity::isOnline() : null,
            'last_push_at'  => optional($configuration->last_push_at)->format('d/m/Y H:i'),
            'last_pull_at'  => optional($configuration->last_pull_at)->format('d/m/Y H:i'),
            'app_version'   => $configuration->app_version,
        ];

        if ($configuration->isClient()) {
            $data['pending']           = SyncQueue::pendingCount();
            $data['stuck']             = SyncQueue::stuckCount();
            $data['pending_documents'] = $configuration->canSync()
                ? (new SyncClient($configuration))->pendingDocumentsCount()
                : 0;
            $data['ranges']            = NumberRangeService::summary();
            $data['conflicts']         = $this->conflicts();
        } else {
            $data['terminals'] = $this->terminals();
        }

        $data['logs'] = SyncLog::query()
            ->orderByDesc('id')
            ->limit(15)
            ->get(['direction', 'entity', 'success', 'records', 'message', 'created_at'])
            ->map(fn (SyncLog $log) => [
                'direction' => $log->direction,
                'entity'    => $log->entity,
                'success'   => $log->success,
                'records'   => $log->records,
                'message'   => $log->message,
                'date'      => $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : null,
            ]);

        return ['success' => true, 'data' => $data];
    }

    /**
     * Sincroniza ahora, sin esperar al ciclo automático.
     */
    public function sync()
    {
        $configuration = OfflineConfiguration::current();

        if (!$configuration->canSync()) {
            return [
                'success' => false,
                'message' => 'El terminal no está pareado con el servidor.',
            ];
        }

        try {
            $summary = (new SyncClient($configuration))->synchronize();
        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        if (empty($summary['online'])) {
            return ['success' => false, 'message' => 'Sin conexión con el servidor.'];
        }

        $sent = ($summary['push']['sent'] ?? 0) + ($summary['documents']['sent'] ?? 0);

        return [
            'success' => $summary['success'],
            'message' => $summary['success']
                ? "Sincronización completa. Se subieron {$sent} registros."
                : 'La sincronización terminó con errores. Revisá el detalle.',
            'summary' => $summary,
        ];
    }

    /**
     * Devuelve a la cola los cambios trabados.
     */
    public function retry()
    {
        $count = SyncQueue::retryStuck();

        return [
            'success' => true,
            'message' => $count > 0
                ? "Se reencolaron {$count} cambios."
                : 'No hay cambios trabados.',
        ];
    }

    /**
     * Reserva un bloque de correlativos para una serie.
     */
    public function allocateRange(Request $request)
    {
        $request->validate([
            'series' => 'required|string|max:10',
            'size'   => 'nullable|integer|min:1|max:10000',
        ]);

        $configuration = OfflineConfiguration::current();

        if (!$configuration->canSync()) {
            return ['success' => false, 'message' => 'El terminal no está pareado con el servidor.'];
        }

        try {
            $range = (new SyncClient($configuration))->allocateRange(
                'document',
                null,
                $request->input('series'),
                (int)$request->input('size', NumberRangeService::DEFAULT_SIZE)
            );
        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        return [
            'success' => true,
            'message' => "Serie {$range->series}: reservados los números {$range->from_number} al {$range->to_number}.",
        ];
    }

    /**
     * Cambios que el servidor rechazó y necesitan que alguien decida.
     */
    private function conflicts(): array
    {
        return SyncQueueItem::stuck()
            ->orderByDesc('id')
            ->limit(50)
            ->get(['id', 'entity', 'entity_id', 'status', 'attempts', 'last_error', 'created_at'])
            ->map(fn (SyncQueueItem $item) => [
                'id'        => $item->id,
                'entity'    => $item->entity,
                'entity_id' => $item->entity_id,
                'status'    => $item->status,
                'attempts'  => $item->attempts,
                'error'     => $item->last_error,
                'date'      => $item->created_at ? $item->created_at->format('d/m/Y H:i') : null,
            ])
            ->toArray();
    }

    /**
     * Terminales dados de alta, para el panel del servidor.
     */
    private function terminals(): array
    {
        return Terminal::query()
            ->orderBy('code')
            ->get()
            ->map(fn (Terminal $terminal) => [
                'code'         => $terminal->code,
                'name'         => $terminal->name,
                'active'       => $terminal->active,
                'app_version'  => $terminal->app_version,
                'last_seen_at' => optional($terminal->last_seen_at)->format('d/m/Y H:i'),
                'minutes_ago'  => $terminal->minutesSinceLastSeen(),
            ])
            ->toArray();
    }
}
