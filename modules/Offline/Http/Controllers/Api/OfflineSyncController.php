<?php

namespace Modules\Offline\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Offline\Models\SyncLog;
use Modules\Offline\Models\Terminal;
use Modules\Offline\Services\EntityRegistry;
use Modules\Offline\Services\NumberRangeService;
use Modules\Offline\Services\SyncApplier;
use Throwable;

/**
 * API que el servidor online expone a los terminales Windows.
 *
 * Todas las rutas van detrás de `auth:api`, así que el token del terminal es
 * el api_token de un usuario del tenant. Además el terminal tiene que estar
 * dado de alta y activo (middleware offline.terminal).
 */
class OfflineSyncController extends Controller
{
    /** Registros por página que devuelve el pull. */
    private const MAX_PULL_LIMIT = 1000;

    /**
     * Prueba de vida. El terminal la usa para decidir si está online.
     */
    public function ping(Request $request)
    {
        $this->touchTerminal($request);

        return [
            'success'     => true,
            'server_time' => now()->toDateTimeString(),
        ];
    }

    /**
     * Alta o actualización del terminal. Es el primer paso del pareo.
     */
    public function handshake(Request $request)
    {
        $request->validate([
            'terminal_code' => 'required|string|max:20',
            'terminal_name' => 'nullable|string|max:100',
            'app_version'   => 'nullable|string|max:40',
        ]);

        $terminal = Terminal::updateOrCreate(
            ['code' => $request->input('terminal_code')],
            [
                'name'         => $request->input('terminal_name'),
                'app_version'  => $request->input('app_version'),
                'user_id'      => optional($request->user())->id,
                'last_ip'      => $request->ip(),
                'last_seen_at' => now(),
            ]
        );

        return [
            'success'     => true,
            'terminal'    => [
                'code'   => $terminal->code,
                'name'   => $terminal->name,
                'active' => $terminal->active,
            ],
            'server_time' => now()->toDateTimeString(),
            'entities'    => array_map(
                fn (array $definition) => $definition['label'],
                EntityRegistry::forDirection(EntityRegistry::PULL)
            ),
        ];
    }

    /**
     * Recibe un lote de cambios del terminal.
     *
     * Cada elemento se procesa por separado: que uno falle no debe frenar a
     * los demás, porque en general el que falla es un caso puntual y el resto
     * son ventas que tienen que llegar igual.
     */
    public function push(Request $request)
    {
        $request->validate([
            'terminal_code'     => 'required|string|max:20',
            'items'             => 'required|array',
            'items.*.uuid'      => 'required|string',
            'items.*.entity'    => 'required|string',
            'items.*.payload'   => 'required|array',
        ]);

        $terminalCode = $request->input('terminal_code');
        $results      = [];
        $applied      = 0;

        foreach ($request->input('items') as $item) {
            try {
                $result = SyncApplier::applyPushed(
                    $item['entity'],
                    $item['payload'],
                    $terminalCode,
                    $item['uuid']
                );

                $applied += empty($result['success']) ? 0 : 1;
            } catch (Throwable $e) {
                Log::error('Offline push falló', [
                    'terminal' => $terminalCode,
                    'entity'   => $item['entity'] ?? null,
                    'uuid'     => $item['uuid'] ?? null,
                    'error'    => $e->getMessage(),
                ]);

                $result = ['success' => false, 'message' => $e->getMessage()];
            }

            $results[] = array_merge(['uuid' => $item['uuid']], $result);
        }

        Terminal::where('code', $terminalCode)->update([
            'last_push_at' => now(),
            'last_seen_at' => now(),
        ]);

        SyncLog::record([
            'terminal_code' => $terminalCode,
            'direction'     => 'push',
            'success'       => $applied === count($results),
            'records'       => $applied,
        ]);

        return [
            'success' => true,
            'applied' => $applied,
            'results' => $results,
        ];
    }

    /**
     * Entrega datos maestros al terminal, paginados por id.
     */
    public function pull(Request $request)
    {
        $request->validate([
            'entity' => 'required|string',
            'since'  => 'nullable|date',
            'cursor' => 'nullable|integer|min:0',
            'limit'  => 'nullable|integer|min:1',
        ]);

        $alias      = $request->input('entity');
        $definition = EntityRegistry::get($alias);

        if (!$definition || $definition['direction'] !== EntityRegistry::PULL) {
            return response()->json([
                'success' => false,
                'message' => "La entidad '{$alias}' no está disponible para descarga.",
            ], 422);
        }

        $model = $definition['model'];

        if (!class_exists($model)) {
            // El módulo que define esa entidad no está instalado en el
            // servidor: se responde vacío en vez de romper la sincronización.
            return [
                'success'     => true,
                'entity'      => $alias,
                'rows'        => [],
                'has_more'    => false,
                'cursor'      => 0,
                'server_time' => now()->toDateTimeString(),
            ];
        }

        /** @var \Illuminate\Database\Eloquent\Model $instance */
        $instance = new $model;
        $table    = $instance->getTable();
        $limit    = min((int)$request->input('limit', 500), self::MAX_PULL_LIMIT);
        $cursor   = (int)$request->input('cursor', 0);

        $query = $instance->getConnection()->table($table)
            ->where('id', '>', $cursor)
            ->orderBy('id')
            ->limit($limit + 1);

        // Solo se filtra por fecha si la tabla la tiene; varias tablas de
        // catálogo del sistema no llevan timestamps.
        $since = $request->input('since');

        if ($since && $instance->getConnection()->getSchemaBuilder()->hasColumn($table, 'updated_at')) {
            $query->where('updated_at', '>=', $since);
        }

        $rows     = $query->get()->map(fn ($row) => (array)$row)->all();
        $hasMore  = count($rows) > $limit;
        $rows     = array_slice($rows, 0, $limit);
        $lastId   = empty($rows) ? $cursor : (int)end($rows)['id'];

        $this->touchTerminal($request, 'pull');

        return [
            'success'     => true,
            'entity'      => $alias,
            'rows'        => $rows,
            'has_more'    => $hasMore,
            'cursor'      => $lastId,
            'server_time' => now()->toDateTimeString(),
        ];
    }

    /**
     * Reserva un bloque de correlativos para el terminal.
     */
    public function allocateRange(Request $request)
    {
        $request->validate([
            'terminal_code'    => 'required|string|max:20',
            'model_alias'      => 'required|string|max:60',
            'document_type_id' => 'nullable|string|max:3',
            'series'           => 'required|string|max:10',
            'size'             => 'nullable|integer|min:1|max:10000',
        ]);

        try {
            $range = NumberRangeService::allocate(
                $request->input('terminal_code'),
                $request->input('model_alias'),
                $request->input('document_type_id'),
                $request->input('series'),
                (int)$request->input('size', NumberRangeService::DEFAULT_SIZE)
            );
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        SyncLog::record([
            'terminal_code' => $request->input('terminal_code'),
            'direction'     => 'ranges',
            'records'       => 1,
            'message'       => "Bloque {$range->series} {$range->from_number}-{$range->to_number}",
        ]);

        return [
            'success' => true,
            'range'   => [
                'uuid'             => $range->uuid,
                'terminal_code'    => $range->terminal_code,
                'model_alias'      => $range->model_alias,
                'document_type_id' => $range->document_type_id,
                'series'           => $range->series,
                'from_number'      => $range->from_number,
                'to_number'        => $range->to_number,
            ],
        ];
    }

    /**
     * El terminal informa hasta qué número consumió cada bloque.
     */
    public function reportRanges(Request $request)
    {
        $request->validate([
            'ranges'                  => 'required|array',
            'ranges.*.uuid'           => 'required|string',
            'ranges.*.current_number' => 'nullable|integer',
        ]);

        $updated = 0;

        foreach ($request->input('ranges') as $range) {
            $result = NumberRangeService::reportProgress(
                $range['uuid'],
                $range['current_number'] ?? null,
                !empty($range['exhausted'])
            );

            $updated += $result ? 1 : 0;
        }

        return ['success' => true, 'updated' => $updated];
    }

    /**
     * Deja constancia de que el terminal se comunicó.
     */
    private function touchTerminal(Request $request, ?string $direction = null): void
    {
        $code = $request->header('X-Terminal-Code') ?: $request->input('terminal_code');

        if (!$code) {
            return;
        }

        $attributes = ['last_seen_at' => now()];

        if ($direction === 'pull') {
            $attributes['last_pull_at'] = now();
        }

        Terminal::where('code', $code)->update($attributes);
    }
}
