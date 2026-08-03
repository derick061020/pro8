<?php

namespace Modules\Offline\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Modules\Offline\Models\OfflineConfiguration;
use Throwable;

/**
 * Engancha la bandeja de salida a los modelos del sistema.
 *
 * Se escuchan los eventos de Eloquent en lugar de agregar un trait a los
 * modelos del core: así el motor offline sobrevive a las actualizaciones que
 * traen versiones nuevas de Document, SaleNote o HotelRent.
 */
class SyncWatcher
{
    private static bool $registered = false;

    /**
     * Evita que el pull de datos maestros vuelva a encolar lo que acaba de
     * bajar del servidor.
     */
    private static bool $muted = false;

    /**
     * Se resuelve una sola vez por request: los eventos de Eloquent se
     * disparan muchas veces y no vale la pena consultar la tabla en cada uno.
     */
    private static ?bool $isClient = null;

    public static function register(): void
    {
        if (self::$registered) {
            return;
        }

        self::$registered = true;

        foreach (EntityRegistry::watched() as $definition) {
            $model = $definition['model'];

            Event::listen("eloquent.created: {$model}", function ($record) {
                self::handle($record, 'create');
            });

            Event::listen("eloquent.updated: {$model}", function ($record) {
                self::handle($record, 'update');
            });
        }
    }

    /**
     * Ejecuta una operación sin que sus escrituras se encolen.
     *
     * @template T
     * @param  callable():T $callback
     * @return T
     */
    public static function muted(callable $callback)
    {
        $previous = self::$muted;
        self::$muted = true;

        try {
            return $callback();
        } finally {
            self::$muted = $previous;
        }
    }

    public static function isMuted(): bool
    {
        return self::$muted;
    }

    private static function handle(Model $record, string $operation): void
    {
        if (self::$muted) {
            return;
        }

        if (!self::isClient()) {
            return;
        }

        SyncQueue::enqueueSafely($record, $operation);
    }

    private static function isClient(): bool
    {
        if (self::$isClient === null) {
            try {
                self::$isClient = OfflineConfiguration::current()->isClient();
            } catch (Throwable $e) {
                // Instalación sin migrar todavía: no hay nada que encolar.
                self::$isClient = false;
            }
        }

        return self::$isClient;
    }
}
