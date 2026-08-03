<?php

namespace Modules\Offline\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Offline\Models\NumberRange;
use Modules\Offline\Models\OfflineConfiguration;
use RuntimeException;

/**
 * Reparto de correlativos entre el servidor online y los terminales offline.
 *
 * El servidor le presta a cada terminal un bloque de números de una serie
 * (por ejemplo del 500 al 999 de B001) y se compromete a no emitirlos él.
 * El terminal consume ese bloque mientras está sin internet, así que cuando
 * sube sus ventas ningún número choca con los que emitió el servidor.
 *
 * El precio de este esquema es que un bloque agotado sin conexión bloquea la
 * emisión, por eso se pide reposición apenas queda poco (ver REFILL_THRESHOLD)
 * y el panel avisa antes de llegar al límite.
 */
class NumberRangeService
{
    /** Tamaño por defecto de un bloque nuevo. */
    public const DEFAULT_SIZE = 500;

    /** Con menos números que esto se pide reposición al servidor. */
    public const REFILL_THRESHOLD = 100;

    /** Debajo de este margen el panel muestra alerta al usuario. */
    public const WARNING_THRESHOLD = 50;

    // -----------------------------------------------------------------------
    // Fachada para el motor de facturación
    //
    // Son los dos únicos métodos que llama el core (Functions::newNumber) y
    // están escritos para no romper nunca la emisión: ante cualquier problema
    // devuelven null / el número original y el sistema sigue con su lógica
    // habitual.
    // -----------------------------------------------------------------------

    /**
     * Correlativo que le toca a este terminal, o null si no aplica.
     */
    public static function nextForModel(string $model, ?string $documentTypeId, string $series): ?int
    {
        try {
            $alias = EntityRegistry::aliasForModel($model, EntityRegistry::PUSH);

            if ($alias === null) {
                return null;
            }

            return self::consumeNext($alias, $documentTypeId, $series);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    /**
     * Corre el correlativo del servidor si pisa un bloque prestado.
     */
    public static function skipReservedForModel(
        string $model,
        ?string $documentTypeId,
        string $series,
        int $number
    ): int {
        try {
            $alias = EntityRegistry::aliasForModel($model, EntityRegistry::PUSH);

            if ($alias === null) {
                return $number;
            }

            return self::skipReserved($alias, $documentTypeId, $series, $number);
        } catch (\Throwable $e) {
            report($e);

            return $number;
        }
    }

    // -----------------------------------------------------------------------
    // Lado terminal
    // -----------------------------------------------------------------------

    /**
     * Bloque vigente del terminal para una serie.
     */
    public static function activeRange(string $modelAlias, ?string $documentTypeId, string $series): ?NumberRange
    {
        return NumberRange::query()
            ->active()
            ->forSeries($modelAlias, $documentTypeId, $series)
            ->orderBy('from_number')
            ->first();
    }

    /**
     * Toma el siguiente correlativo del bloque reservado y lo marca consumido.
     *
     * Devuelve null cuando esta instalación no es un terminal o cuando no hay
     * bloque disponible; en ese caso el sistema sigue con su numeración normal.
     */
    public static function consumeNext(string $modelAlias, ?string $documentTypeId, string $series): ?int
    {
        $configuration = OfflineConfiguration::current();

        if (!$configuration->isClient()) {
            return null;
        }

        return DB::transaction(function () use ($modelAlias, $documentTypeId, $series) {
            $range = NumberRange::query()
                ->active()
                ->forSeries($modelAlias, $documentTypeId, $series)
                ->orderBy('from_number')
                ->lockForUpdate()
                ->first();

            if (!$range) {
                return null;
            }

            $number = $range->nextNumber();

            if ($number === null) {
                $range->update([
                    'status'       => NumberRange::STATUS_EXHAUSTED,
                    'exhausted_at' => now(),
                ]);

                return null;
            }

            $range->consume($number);

            return $number;
        });
    }

    /**
     * Series cuyo bloque está por agotarse y conviene reponer.
     *
     * @return array<int, array{model_alias:string, document_type_id:?string, series:string, remaining:int}>
     */
    public static function seriesNeedingRefill(int $threshold = self::REFILL_THRESHOLD): array
    {
        $pending = [];

        foreach (NumberRange::query()->active()->get() as $range) {
            if ($range->remaining() > $threshold) {
                continue;
            }

            $pending[] = [
                'model_alias'      => $range->model_alias,
                'document_type_id' => $range->document_type_id,
                'series'           => $range->series,
                'remaining'        => $range->remaining(),
            ];
        }

        return $pending;
    }

    /**
     * Resumen para el panel: cuánto queda de cada bloque.
     */
    public static function summary(): array
    {
        return NumberRange::query()
            ->active()
            ->orderBy('series')
            ->get()
            ->map(fn (NumberRange $range) => [
                'series'           => $range->series,
                'document_type_id' => $range->document_type_id,
                'from_number'      => $range->from_number,
                'to_number'        => $range->to_number,
                'current_number'   => $range->current_number,
                'remaining'        => $range->remaining(),
                'total'            => $range->total(),
                'warning'          => $range->remaining() <= self::WARNING_THRESHOLD,
            ])
            ->toArray();
    }

    /**
     * Guarda localmente un bloque entregado por el servidor.
     */
    public static function storeAllocated(array $payload): NumberRange
    {
        return NumberRange::updateOrCreate(
            ['uuid' => $payload['uuid']],
            [
                'terminal_code'    => $payload['terminal_code'],
                'model_alias'      => $payload['model_alias'],
                'document_type_id' => $payload['document_type_id'] ?? null,
                'series'           => $payload['series'],
                'from_number'      => $payload['from_number'],
                'to_number'        => $payload['to_number'],
                'status'           => NumberRange::STATUS_ACTIVE,
                'allocated_at'     => now(),
            ]
        );
    }

    // -----------------------------------------------------------------------
    // Lado servidor
    // -----------------------------------------------------------------------

    /**
     * Reserva un bloque para un terminal.
     *
     * El bloque arranca después del último número usado en esa serie y después
     * del último bloque ya reservado, de modo que nunca se solapan.
     */
    public static function allocate(
        string $terminalCode,
        string $modelAlias,
        ?string $documentTypeId,
        string $series,
        int $size = self::DEFAULT_SIZE
    ): NumberRange {
        if ($size < 1) {
            throw new RuntimeException('El tamaño del bloque debe ser mayor que cero.');
        }

        return DB::transaction(function () use ($terminalCode, $modelAlias, $documentTypeId, $series, $size) {
            $start = self::nextFreeNumber($modelAlias, $documentTypeId, $series);

            return NumberRange::create([
                'uuid'             => (string)Str::uuid(),
                'terminal_code'    => $terminalCode,
                'model_alias'      => $modelAlias,
                'document_type_id' => $documentTypeId,
                'series'           => $series,
                'from_number'      => $start,
                'to_number'        => $start + $size - 1,
                'status'           => NumberRange::STATUS_ACTIVE,
                'allocated_at'     => now(),
            ]);
        });
    }

    /**
     * Primer número libre de una serie: mayor entre el último emitido y el
     * final del último bloque reservado.
     */
    private static function nextFreeNumber(string $modelAlias, ?string $documentTypeId, string $series): int
    {
        $lastReserved = (int)NumberRange::query()
            ->forSeries($modelAlias, $documentTypeId, $series)
            ->max('to_number');

        $lastIssued = 0;

        $model = EntityRegistry::modelFor($modelAlias);

        if ($model && class_exists($model)) {
            $query = $model::query()->where('series', $series);

            if ($documentTypeId !== null && self::hasColumn($model, 'document_type_id')) {
                $query->where('document_type_id', $documentTypeId);
            }

            $lastIssued = (int)$query->max('number');
        }

        return max($lastReserved, $lastIssued) + 1;
    }

    /**
     * Corre un correlativo hacia adelante si cae dentro de un bloque prestado
     * a un terminal. Es lo que impide que el servidor emita un número que un
     * terminal ya tiene reservado.
     */
    public static function skipReserved(
        string $modelAlias,
        ?string $documentTypeId,
        string $series,
        int $number
    ): int {
        $ranges = NumberRange::query()
            ->forSeries($modelAlias, $documentTypeId, $series)
            ->whereIn('status', [NumberRange::STATUS_ACTIVE, NumberRange::STATUS_EXHAUSTED])
            ->orderBy('from_number')
            ->get();

        foreach ($ranges as $range) {
            if ($number >= $range->from_number && $number <= $range->to_number) {
                $number = $range->to_number + 1;
            }
        }

        return $number;
    }

    /**
     * Registra el avance informado por un terminal, para que el servidor sepa
     * qué parte del bloque ya se usó.
     */
    public static function reportProgress(string $uuid, ?int $currentNumber, bool $exhausted = false): ?NumberRange
    {
        $range = NumberRange::where('uuid', $uuid)->first();

        if (!$range) {
            return null;
        }

        $range->current_number = $currentNumber;
        $range->reported_at    = now();

        if ($exhausted || ($currentNumber !== null && $currentNumber >= $range->to_number)) {
            $range->status       = NumberRange::STATUS_EXHAUSTED;
            $range->exhausted_at = now();
        }

        $range->save();

        return $range;
    }

    private static function hasColumn(string $model, string $column): bool
    {
        try {
            /** @var \Illuminate\Database\Eloquent\Model $instance */
            $instance = new $model;

            return $instance->getConnection()
                ->getSchemaBuilder()
                ->hasColumn($instance->getTable(), $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
