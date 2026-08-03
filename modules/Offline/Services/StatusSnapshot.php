<?php

namespace Modules\Offline\Services;

use Modules\Offline\Models\OfflineConfiguration;
use Throwable;

/**
 * Foto del estado del terminal.
 *
 * El demonio la deja escrita en disco después de cada ciclo para que el
 * launcher de Windows pueda mostrar el estado en la bandeja del sistema sin
 * levantar una sesión HTTP ni resolver el tenant por su cuenta.
 *
 * El formato del archivo es contrato con desktop/launcher: si se cambia,
 * hay que actualizar también el launcher.
 */
class StatusSnapshot
{
    /** Ruta del archivo, relativa a storage/app. */
    private const FILENAME = 'offline-status.json';

    public static function path(): string
    {
        return storage_path('app' . DIRECTORY_SEPARATOR . self::FILENAME);
    }

    /**
     * Arma el estado actual.
     */
    public static function build(): array
    {
        $configuration = OfflineConfiguration::current();

        $status = [
            'mode'          => $configuration->mode,
            'terminal_code' => $configuration->terminal_code,
            'terminal_name' => $configuration->terminal_name,
            'server'        => $configuration->serverUrl(),
            'paired'        => $configuration->canSync(),
            'online'        => $configuration->canSync() ? Connectivity::isOnline() : false,
            'last_push_at'  => optional($configuration->last_push_at)->toDateTimeString(),
            'last_pull_at'  => optional($configuration->last_pull_at)->toDateTimeString(),
            'app_version'   => $configuration->app_version,
            'updated_at'    => now()->toDateTimeString(),
        ];

        if ($configuration->isClient()) {
            $status['pending']           = SyncQueue::pendingCount();
            $status['stuck']             = SyncQueue::stuckCount();
            $status['pending_documents'] = $configuration->canSync()
                ? (new SyncClient($configuration))->pendingDocumentsCount()
                : 0;
            $status['ranges']            = NumberRangeService::summary();
        }

        return $status;
    }

    /**
     * Escribe el estado en disco. No propaga errores: que falle la foto no
     * puede tumbar la sincronización.
     */
    public static function write(?array $status = null): void
    {
        try {
            $status = $status ?: self::build();

            file_put_contents(
                self::path(),
                json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
        } catch (Throwable $e) {
            report($e);
        }
    }
}
