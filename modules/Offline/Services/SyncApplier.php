<?php

namespace Modules\Offline\Services;

use Illuminate\Database\Connection;
use Modules\Offline\Models\SyncIdMap;
use RuntimeException;

/**
 * Escritura de datos sincronizados en la base local.
 *
 * Trabaja con el query builder y no con Eloquent: los registros ya vienen
 * como filas crudas, y así se evita disparar observers, mutadores y la propia
 * bandeja de salida (un dato bajado del servidor no debe volver a subir).
 */
class SyncApplier
{
    /** @var array<string, array<int, string>> columnas por tabla, memorizadas */
    private static array $columns = [];

    // -----------------------------------------------------------------------
    // Lado terminal: aplicar lo que bajó del servidor
    // -----------------------------------------------------------------------

    /**
     * Inserta o actualiza datos maestros conservando el id del servidor.
     *
     * Conservar el id es lo que permite que `item_id`, `hotel_room_id` o
     * `establishment_id` signifiquen lo mismo en las dos bases y no haya que
     * traducir claves foráneas en cada venta.
     *
     * @param  array<int, array<string, mixed>> $rows
     * @return int registros aplicados
     */
    public static function applyPulled(string $alias, array $rows): int
    {
        $definition = EntityRegistry::get($alias);

        if (!$definition || $definition['direction'] !== EntityRegistry::PULL) {
            throw new RuntimeException("La entidad '{$alias}' no se puede bajar del servidor.");
        }

        [$connection, $table] = self::target($definition['model']);
        $columns = self::columns($connection, $table);

        $applied = 0;

        $connection->transaction(function () use ($rows, $connection, $table, $columns, &$applied) {
            foreach ($rows as $row) {
                if (!isset($row['id'])) {
                    continue;
                }

                $values = array_intersect_key($row, array_flip($columns));
                unset($values['id']);

                $connection->table($table)->updateOrInsert(['id' => $row['id']], $values);
                $applied++;
            }
        });

        return $applied;
    }

    // -----------------------------------------------------------------------
    // Lado servidor: aplicar lo que subió un terminal
    // -----------------------------------------------------------------------

    /**
     * Guarda en el servidor un registro creado en un terminal.
     *
     * Es idempotente: si el mismo uuid o el mismo id de terminal ya se aplicó,
     * devuelve el id remoto existente en vez de duplicar.
     *
     * @return array{success:bool, remote_id?:int, message?:string, conflict?:bool}
     */
    public static function applyPushed(string $alias, array $payload, string $terminalCode, string $uuid): array
    {
        $definition = EntityRegistry::get($alias);

        if (!$definition || $definition['direction'] !== EntityRegistry::PUSH) {
            return ['success' => false, 'message' => "La entidad '{$alias}' no se acepta desde terminales."];
        }

        $attributes = $payload['attributes'] ?? [];
        $localId    = (int)($attributes['id'] ?? 0);

        if ($localId === 0) {
            return ['success' => false, 'message' => 'El registro llegó sin id de origen.'];
        }

        // Ya aplicado en un envío anterior que no alcanzó a confirmarse.
        $known = SyncIdMap::toRemote($alias, $localId, $terminalCode);

        if ($known !== null) {
            return ['success' => true, 'remote_id' => $known];
        }

        [$connection, $table] = self::target($definition['model']);

        $conflict = self::detectConflict($definition, $attributes, $connection);

        if ($conflict !== null) {
            return ['success' => false, 'conflict' => true, 'message' => $conflict];
        }

        $remoteId = $connection->transaction(function () use (
            $definition, $attributes, $payload, $connection, $table, $terminalCode, $alias, $localId, $uuid
        ) {
            $values = self::prepareValues($attributes, $connection, $table, $definition, $terminalCode);

            // Clave natural: evita duplicar, por ejemplo, un cliente que el
            // terminal creó offline y que en el servidor ya existía.
            $existingId = self::findByNaturalKey($definition, $values, $connection, $table);

            if ($existingId !== null) {
                $connection->table($table)->where('id', $existingId)->update($values);
                $remoteId = $existingId;
            } else {
                $remoteId = (int)$connection->table($table)->insertGetId($values);
            }

            self::applyChildren($definition['children'] ?? [], $payload['children'] ?? [], $remoteId, $terminalCode);

            SyncIdMap::remember($alias, $localId, $remoteId, $uuid, $terminalCode);

            return $remoteId;
        });

        return ['success' => true, 'remote_id' => $remoteId];
    }

    /**
     * Guarda los hijos del registro (items de una venta, consumos de una
     * estadía, pagos de un consumo).
     *
     * Se borran y reinsertan: mucho más simple que diferenciar altas de
     * modificaciones, y correcto porque el terminal siempre manda el conjunto
     * completo del registro.
     */
    private static function applyChildren(
        array $definitions,
        array $groups,
        int $parentId,
        string $terminalCode
    ): void {
        foreach ($definitions as $relation => $childDefinition) {
            if (!isset($groups[$relation]) || empty($childDefinition['model'])) {
                continue;
            }

            [$childConnection, $childTable] = self::target($childDefinition['model']);
            $foreignKey = $childDefinition['fk'];

            $childConnection->table($childTable)->where($foreignKey, $parentId)->delete();

            foreach ($groups[$relation] as $child) {
                $values = self::prepareValues(
                    $child['attributes'] ?? [],
                    $childConnection,
                    $childTable,
                    $childDefinition,
                    $terminalCode
                );

                $values[$foreignKey] = $parentId;

                $childId = (int)$childConnection->table($childTable)->insertGetId($values);

                self::applyChildren(
                    $childDefinition['children'] ?? [],
                    $child['children'] ?? [],
                    $childId,
                    $terminalCode
                );
            }
        }
    }

    /**
     * Deja los atributos listos para insertar: descarta columnas que no
     * existen, quita el id de origen y traduce las claves foráneas que apuntan
     * a otros registros creados en el terminal.
     */
    private static function prepareValues(
        array $attributes,
        Connection $connection,
        string $table,
        array $definition,
        string $terminalCode
    ): array {
        $values = array_intersect_key($attributes, array_flip(self::columns($connection, $table)));

        unset($values['id']);

        foreach ($definition['translate'] ?? [] as $column => $entity) {
            if (empty($values[$column])) {
                continue;
            }

            $translated = SyncIdMap::toRemote($entity, $values[$column], $terminalCode);

            if ($translated !== null) {
                $values[$column] = $translated;
            }
        }

        return $values;
    }

    /**
     * Busca un registro equivalente ya existente en el servidor según la clave
     * natural declarada en el catálogo.
     */
    private static function findByNaturalKey(
        array $definition,
        array $values,
        Connection $connection,
        string $table
    ): ?int {
        $keys = $definition['match'] ?? [];

        if (empty($keys)) {
            return null;
        }

        $query = $connection->table($table);

        foreach ($keys as $key) {
            if (!array_key_exists($key, $values) || $values[$key] === null || $values[$key] === '') {
                return null;
            }

            $query->where($key, $values[$key]);
        }

        $id = $query->value('id');

        return $id ? (int)$id : null;
    }

    /**
     * Conflictos que no se pueden resolver solos y necesitan que una persona
     * decida. Por ahora solo el de hotel: dos terminales alquilando la misma
     * habitación en el mismo tramo mientras estuvieron incomunicados.
     */
    private static function detectConflict(array $definition, array $attributes, Connection $connection): ?string
    {
        if (($definition['conflict'] ?? null) !== 'hotel_room_overlap') {
            return null;
        }

        $roomId = $attributes['hotel_room_id'] ?? null;
        $start  = $attributes['rental_date_time'] ?? null;

        if (!$roomId || !$start) {
            return null;
        }

        $overlap = $connection->table('hotel_rents')
            ->where('hotel_room_id', $roomId)
            ->whereNull('output_date')
            ->where('rental_date_time', '<=', $start)
            ->exists();

        if (!$overlap) {
            return null;
        }

        return 'La habitación ya figura ocupada en el servidor para esa fecha. '
             . 'Revisá la estadía manualmente antes de subirla.';
    }

    // -----------------------------------------------------------------------
    // Utilidades
    // -----------------------------------------------------------------------

    /**
     * @return array{0: Connection, 1: string}
     */
    private static function target(string $model): array
    {
        if (!class_exists($model)) {
            throw new RuntimeException("El modelo {$model} no está disponible en esta instalación.");
        }

        /** @var \Illuminate\Database\Eloquent\Model $instance */
        $instance = new $model;

        return [$instance->getConnection(), $instance->getTable()];
    }

    /**
     * @return array<int, string>
     */
    private static function columns(Connection $connection, string $table): array
    {
        $key = $connection->getName() . '.' . $table;

        if (!isset(self::$columns[$key])) {
            self::$columns[$key] = $connection->getSchemaBuilder()->getColumnListing($table);
        }

        return self::$columns[$key];
    }
}
