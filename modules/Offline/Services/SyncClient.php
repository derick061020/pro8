<?php

namespace Modules\Offline\Services;

use App\Models\Tenant\Document;
use Facades\App\Http\Controllers\Tenant\DocumentController as DocumentControllerFacade;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Modules\Offline\Models\NumberRange;
use Modules\Offline\Models\OfflineConfiguration;
use Modules\Offline\Models\PullState;
use Modules\Offline\Models\SyncIdMap;
use Modules\Offline\Models\SyncLog;
use Modules\Offline\Models\SyncQueueItem;
use RuntimeException;
use Throwable;

/**
 * Lado terminal de la sincronización.
 *
 * Sube lo que se vendió sin internet, baja los datos maestros del servidor y
 * pide reposición de correlativos. Todo lo que hace queda anotado en
 * offline_sync_logs.
 */
class SyncClient
{
    /** Cambios que viajan por request al subir. */
    private const PUSH_BATCH = 25;

    /** Registros que se piden por página al bajar. */
    private const PULL_BATCH = 500;

    private const TIMEOUT = 60;

    private OfflineConfiguration $configuration;

    public function __construct(?OfflineConfiguration $configuration = null)
    {
        $this->configuration = $configuration ?: OfflineConfiguration::current();
    }

    // -----------------------------------------------------------------------
    // Ciclo completo
    // -----------------------------------------------------------------------

    /**
     * Ejecuta una sincronización completa: primero sube (lo urgente es que la
     * venta llegue al servidor), después baja maestros y repone correlativos.
     *
     * @return array resumen de lo hecho
     */
    public function synchronize(): array
    {
        $this->assertClient();

        if (!Connectivity::isOnline(true)) {
            return [
                'success' => false,
                'online'  => false,
                'message' => 'Sin conexión con el servidor.',
            ];
        }

        $summary = [
            'success'   => true,
            'online'    => true,
            'documents' => $this->pushDocuments(),
            'push'      => $this->push(),
            'pull'      => $this->pull(),
            'ranges'    => $this->refillRanges(),
        ];

        $summary['success'] = $summary['push']['success']
            && $summary['pull']['success']
            && $summary['documents']['success'];

        return $summary;
    }

    // -----------------------------------------------------------------------
    // Subida
    // -----------------------------------------------------------------------

    /**
     * Sube los comprobantes electrónicos emitidos sin conexión.
     *
     * Esta parte no usa la bandeja de salida: el sistema ya trae su propio
     * canal (`Document::send_server` + `DocumentController::sendServer`), que
     * reconstruye el comprobante en el servidor con CoreFacturalo, lo firma y
     * lo manda a SUNAT. Se reutiliza tal cual porque es la pieza con
     * implicancias tributarias y no conviene tener dos implementaciones.
     */
    public function pushDocuments(): array
    {
        $this->assertClient();

        $documents = Document::query()
            ->where('send_server', 0)
            ->where('success_shipping_status', false)
            ->orderBy('id')
            ->get();

        $sent   = 0;
        $failed = 0;
        $error  = null;

        foreach ($documents as $document) {
            try {
                $response = DocumentControllerFacade::sendServer($document->id);

                if (!empty($response['success'])) {
                    $document->success_shipping_status = true;
                    $document->shipping_status = json_encode(array_merge($response, [
                        'message' => 'El envío al servidor online fue exitoso',
                    ]));
                    $sent++;
                } else {
                    $document->success_shipping_status = false;
                    $document->shipping_status = json_encode($response);
                    $error = $response['message'] ?? 'El servidor rechazó el comprobante.';
                    $failed++;
                }

                $document->save();
            } catch (Throwable $e) {
                $document->success_shipping_status = false;
                $document->shipping_status = json_encode([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
                $document->save();

                $error = $e->getMessage();
                $failed++;
            }
        }

        if ($sent > 0 || $failed > 0) {
            SyncLog::record([
                'terminal_code' => $this->configuration->terminal_code,
                'direction'     => 'push',
                'entity'        => 'document',
                'success'       => $failed === 0,
                'records'       => $sent,
                'message'       => $error,
            ]);
        }

        return [
            'success' => $failed === 0,
            'sent'    => $sent,
            'failed'  => $failed,
            'pending' => $this->pendingDocumentsCount(),
            'message' => $error,
        ];
    }

    /**
     * Comprobantes emitidos que todavía no llegaron al servidor.
     */
    public function pendingDocumentsCount(): int
    {
        return Document::query()
            ->where('send_server', 0)
            ->where('success_shipping_status', false)
            ->count();
    }

    /**
     * Envía los cambios encolados. Trabaja por lotes hasta vaciar la cola o
     * hasta que un lote falle entero (típicamente porque se cortó la red).
     */
    public function push(): array
    {
        $this->assertClient();

        $sent     = 0;
        $failed   = 0;
        $started  = microtime(true);
        $lastError = null;

        while (true) {
            $items = SyncQueueItem::readyToSend()->limit(self::PUSH_BATCH)->get();

            if ($items->isEmpty()) {
                break;
            }

            try {
                $results = $this->sendBatch($items);
            } catch (Throwable $e) {
                $lastError = $e->getMessage();

                // Falla de transporte: no es culpa de los datos, se reintenta
                // en el próximo ciclo sin quemar intentos de cada elemento.
                foreach ($items as $item) {
                    $item->update(['next_attempt_at' => Carbon::now()->addMinute()]);
                }

                $failed += $items->count();
                break;
            }

            foreach ($items as $item) {
                $result = $results[$item->uuid] ?? null;

                if ($result === null) {
                    $item->markFailed('El servidor no respondió por este registro.');
                    $failed++;
                    continue;
                }

                if (!empty($result['success'])) {
                    $item->markSynced($result['remote_id'] ?? null);

                    if (!empty($result['remote_id'])) {
                        SyncIdMap::remember($item->entity, $item->entity_id, (int)$result['remote_id'], $item->uuid);
                    }

                    $sent++;
                    continue;
                }

                $item->markFailed(
                    $result['message'] ?? 'Rechazado por el servidor.',
                    !empty($result['conflict'])
                );
                $lastError = $result['message'] ?? $lastError;
                $failed++;
            }
        }

        if ($sent > 0) {
            $this->configuration->forceFill(['last_push_at' => now()])->save();
        }

        SyncLog::record([
            'terminal_code' => $this->configuration->terminal_code,
            'direction'     => 'push',
            'success'       => $failed === 0,
            'records'       => $sent,
            'message'       => $lastError,
            'duration_ms'   => (int)((microtime(true) - $started) * 1000),
        ]);

        return [
            'success' => $failed === 0,
            'sent'    => $sent,
            'failed'  => $failed,
            'pending' => SyncQueue::pendingCount(),
            'message' => $lastError,
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, SyncQueueItem> $items
     * @return array<string, array> resultados indexados por uuid
     */
    private function sendBatch($items): array
    {
        $payload = $items->map(fn (SyncQueueItem $item) => [
            'uuid'      => $item->uuid,
            'entity'    => $item->entity,
            'entity_id' => $item->entity_id,
            'operation' => $item->operation,
            'payload'   => $item->payload,
        ])->values()->all();

        $response = $this->request('POST', '/api/offline/push', [
            'terminal_code' => $this->configuration->terminal_code,
            'items'         => $payload,
        ]);

        $results = [];

        foreach ($response['results'] ?? [] as $result) {
            if (!empty($result['uuid'])) {
                $results[$result['uuid']] = $result;
            }
        }

        return $results;
    }

    // -----------------------------------------------------------------------
    // Bajada
    // -----------------------------------------------------------------------

    /**
     * Baja los datos maestros. Cada entidad se pide desde su última marca de
     * tiempo, así que la segunda corrida ya es incremental.
     */
    public function pull(): array
    {
        $this->assertClient();

        $total   = 0;
        $errors  = [];
        $started = microtime(true);

        foreach (EntityRegistry::forDirection(EntityRegistry::PULL) as $alias => $definition) {
            try {
                $total += $this->pullEntity($alias);
            } catch (Throwable $e) {
                $errors[] = "{$definition['label']}: {$e->getMessage()}";
            }
        }

        if ($total > 0 || empty($errors)) {
            $this->configuration->forceFill(['last_pull_at' => now()])->save();
        }

        SyncLog::record([
            'terminal_code' => $this->configuration->terminal_code,
            'direction'     => 'pull',
            'success'       => empty($errors),
            'records'       => $total,
            'message'       => $errors ? implode(' | ', $errors) : null,
            'duration_ms'   => (int)((microtime(true) - $started) * 1000),
        ]);

        return [
            'success' => empty($errors),
            'records' => $total,
            'message' => $errors ? implode(' | ', $errors) : null,
        ];
    }

    /**
     * Baja una entidad completa, paginando hasta que el servidor no tenga más.
     */
    private function pullEntity(string $alias): int
    {
        $state   = PullState::forEntity($alias);
        $applied = 0;
        $cursor  = 0;

        do {
            $response = $this->request('GET', '/api/offline/pull', [
                'entity' => $alias,
                'since'  => optional($state->last_synced_at)->toDateTimeString(),
                'cursor' => $cursor,
                'limit'  => self::PULL_BATCH,
            ]);

            $rows = $response['rows'] ?? [];

            if (!empty($rows)) {
                $applied += SyncApplier::applyPulled($alias, $rows);
            }

            $cursor  = $response['cursor'] ?? 0;
            $hasMore = !empty($response['has_more']);
        } while ($hasMore);

        $state->update([
            'last_synced_at' => $response['server_time'] ?? now(),
            'records'        => $state->records + $applied,
        ]);

        return $applied;
    }

    // -----------------------------------------------------------------------
    // Correlativos
    // -----------------------------------------------------------------------

    /**
     * Informa el avance de los bloques y pide reposición de los que están por
     * agotarse. Se ejecuta en cada ciclo, así que en operación normal el
     * terminal nunca se queda sin números.
     */
    public function refillRanges(): array
    {
        $this->assertClient();

        $this->reportRangeProgress();

        $requested = 0;
        $errors    = [];

        foreach (NumberRangeService::seriesNeedingRefill() as $series) {
            try {
                $response = $this->request('POST', '/api/offline/ranges/allocate', [
                    'terminal_code'    => $this->configuration->terminal_code,
                    'model_alias'      => $series['model_alias'],
                    'document_type_id' => $series['document_type_id'],
                    'series'           => $series['series'],
                    'size'             => NumberRangeService::DEFAULT_SIZE,
                ]);

                if (!empty($response['range'])) {
                    NumberRangeService::storeAllocated($response['range']);
                    $requested++;
                }
            } catch (Throwable $e) {
                $errors[] = "{$series['series']}: {$e->getMessage()}";
            }
        }

        SyncLog::record([
            'terminal_code' => $this->configuration->terminal_code,
            'direction'     => 'ranges',
            'success'       => empty($errors),
            'records'       => $requested,
            'message'       => $errors ? implode(' | ', $errors) : null,
        ]);

        return [
            'success'   => empty($errors),
            'allocated' => $requested,
            'message'   => $errors ? implode(' | ', $errors) : null,
        ];
    }

    /**
     * Pide explícitamente un bloque para una serie. Lo usa el pareo inicial y
     * el botón del panel.
     */
    public function allocateRange(
        string $modelAlias,
        ?string $documentTypeId,
        string $series,
        int $size = NumberRangeService::DEFAULT_SIZE
    ): NumberRange {
        $this->assertClient();

        $response = $this->request('POST', '/api/offline/ranges/allocate', [
            'terminal_code'    => $this->configuration->terminal_code,
            'model_alias'      => $modelAlias,
            'document_type_id' => $documentTypeId,
            'series'           => $series,
            'size'             => $size,
        ]);

        if (empty($response['range'])) {
            throw new RuntimeException($response['message'] ?? 'El servidor no entregó el bloque de numeración.');
        }

        return NumberRangeService::storeAllocated($response['range']);
    }

    /**
     * Le cuenta al servidor hasta qué número consumió cada bloque.
     */
    private function reportRangeProgress(): void
    {
        $ranges = NumberRange::query()
            ->whereIn('status', [NumberRange::STATUS_ACTIVE, NumberRange::STATUS_EXHAUSTED])
            ->whereNotNull('current_number')
            ->get();

        if ($ranges->isEmpty()) {
            return;
        }

        try {
            $this->request('POST', '/api/offline/ranges/report', [
                'terminal_code' => $this->configuration->terminal_code,
                'ranges'        => $ranges->map(fn (NumberRange $range) => [
                    'uuid'           => $range->uuid,
                    'current_number' => $range->current_number,
                    'exhausted'      => $range->status === NumberRange::STATUS_EXHAUSTED,
                ])->all(),
            ]);
        } catch (Throwable $e) {
            // Informar el avance es best-effort: si falla, se reintenta solo
            // en el siguiente ciclo.
        }
    }

    // -----------------------------------------------------------------------
    // Pareo
    // -----------------------------------------------------------------------

    /**
     * Da de alta este terminal en el servidor y trae su configuración.
     */
    public function handshake(): array
    {
        $response = $this->request('POST', '/api/offline/handshake', [
            'terminal_code' => $this->configuration->terminal_code,
            'terminal_name' => $this->configuration->terminal_name,
            'app_version'   => $this->configuration->app_version,
        ]);

        SyncLog::record([
            'terminal_code' => $this->configuration->terminal_code,
            'direction'     => 'ping',
            'success'       => !empty($response['success']),
            'message'       => $response['message'] ?? null,
        ]);

        return $response;
    }

    // -----------------------------------------------------------------------
    // Transporte
    // -----------------------------------------------------------------------

    /**
     * @throws RuntimeException si el servidor responde con error
     */
    private function request(string $method, string $uri, array $data = []): array
    {
        $client = new Client([
            'base_uri'    => $this->configuration->serverUrl(),
            'timeout'     => self::TIMEOUT,
            'verify'      => false,
            'http_errors' => false,
        ]);

        $options = [
            'headers' => [
                'Authorization'   => 'Bearer ' . $this->configuration->token_server,
                'Accept'          => 'application/json',
                'X-Terminal-Code' => (string)$this->configuration->terminal_code,
            ],
        ];

        if ($method === 'GET') {
            $options['query'] = array_filter($data, fn ($value) => $value !== null);
        } else {
            $options['json'] = $data;
        }

        $response = $client->request($method, $uri, $options);
        $status   = $response->getStatusCode();
        $body     = (string)$response->getBody();
        $decoded  = json_decode($body, true);

        if ($status === 401 || $status === 403) {
            throw new RuntimeException('El servidor rechazó el token del terminal. Volvé a parear la instalación.');
        }

        if ($status >= 400 || !is_array($decoded)) {
            $message = is_array($decoded) && !empty($decoded['message'])
                ? $decoded['message']
                : "El servidor respondió {$status}.";

            throw new RuntimeException($message);
        }

        return $decoded;
    }

    private function assertClient(): void
    {
        if (!$this->configuration->canSync()) {
            throw new RuntimeException(
                'Esta instalación no está configurada como terminal offline o le falta el pareo con el servidor.'
            );
        }
    }
}
