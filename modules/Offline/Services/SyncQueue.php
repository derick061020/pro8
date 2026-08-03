<?php

namespace Modules\Offline\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Offline\Models\OfflineConfiguration;
use Modules\Offline\Models\SyncQueueItem;
use Throwable;

/**
 * Bandeja de salida del terminal.
 *
 * Toma un registro local, lo serializa junto con sus hijos y lo deja
 * encolado para el próximo envío al servidor online.
 */
class SyncQueue
{
    /**
     * Encola un cambio. Devuelve null si esta instalación no es un terminal
     * o si la entidad no está en el catálogo de sincronización.
     */
    public static function enqueue(Model $model, string $operation = 'create'): ?SyncQueueItem
    {
        $configuration = OfflineConfiguration::current();

        if (!$configuration->isClient()) {
            return null;
        }

        $alias = EntityRegistry::aliasForModel(get_class($model), EntityRegistry::PUSH);

        if ($alias === null) {
            return null;
        }

        $definition = EntityRegistry::get($alias);

        // Si el registro ya está encolado y todavía no salió, se actualiza esa
        // entrada en vez de generar una segunda: al terminal le importa el
        // estado final del registro, no cada paso intermedio.
        $pending = SyncQueueItem::query()
            ->where('entity', $alias)
            ->where('entity_id', $model->getKey())
            ->whereIn('status', [SyncQueueItem::STATUS_PENDING, SyncQueueItem::STATUS_FAILED])
            ->first();

        $payload = self::serialize($model, $definition);

        if ($pending) {
            $pending->update([
                'payload'   => $payload,
                'operation' => $pending->operation === 'create' ? 'create' : $operation,
            ]);

            return $pending;
        }

        return SyncQueueItem::create([
            'uuid'          => (string)Str::uuid(),
            'terminal_code' => $configuration->terminal_code,
            'entity'        => $alias,
            'entity_id'     => $model->getKey(),
            'operation'     => $operation,
            'payload'       => $payload,
            'priority'      => $definition['priority'] ?? 100,
            'status'        => SyncQueueItem::STATUS_PENDING,
        ]);
    }

    /**
     * Encola sin dejar que un error rompa la operación del usuario: si la
     * sincronización falla, la venta igual tiene que poder cerrarse.
     */
    public static function enqueueSafely(Model $model, string $operation = 'create'): void
    {
        try {
            self::enqueue($model, $operation);
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * Convierte el modelo y sus hijos en el arreglo que viaja al servidor.
     */
    public static function serialize(Model $model, array $definition): array
    {
        $payload = [
            'attributes' => $model->getAttributes(),
            'children'   => [],
        ];

        foreach ($definition['children'] ?? [] as $relation => $childDefinition) {
            if (!method_exists($model, $relation)) {
                continue;
            }

            $payload['children'][$relation] = self::serializeChildren($model, $relation, $childDefinition);
        }

        return $payload;
    }

    /**
     * @return array<int, array>
     */
    private static function serializeChildren(Model $model, string $relation, array $childDefinition): array
    {
        $rows = [];

        foreach ($model->{$relation}()->get() as $child) {
            $row = [
                'attributes' => $child->getAttributes(),
                'children'   => [],
            ];

            foreach ($childDefinition['children'] ?? [] as $nested => $nestedDefinition) {
                if (!method_exists($child, $nested)) {
                    continue;
                }

                $row['children'][$nested] = self::serializeChildren($child, $nested, $nestedDefinition);
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Cantidad de cambios esperando subir.
     */
    public static function pendingCount(): int
    {
        return SyncQueueItem::readyToSend()->count();
    }

    /**
     * Cambios que ya no se reintentan solos y necesitan que alguien mire.
     */
    public static function stuckCount(): int
    {
        return SyncQueueItem::stuck()->count();
    }

    /**
     * Devuelve a la cola los elementos trabados, reiniciando sus intentos.
     * Es lo que ejecuta el botón "Reintentar" del panel.
     */
    public static function retryStuck(): int
    {
        return SyncQueueItem::stuck()->update([
            'status'          => SyncQueueItem::STATUS_PENDING,
            'attempts'        => 0,
            'next_attempt_at' => null,
        ]);
    }
}
