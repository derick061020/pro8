<?php

namespace Modules\Offline\Models;

use App\Models\Tenant\ModelTenant;
use Throwable;

/**
 * Bitácora de sincronización, para diagnosticar en sitio sin abrir los logs
 * de Laravel.
 */
class SyncLog extends ModelTenant
{
    protected $table = 'offline_sync_logs';

    protected $fillable = [
        'terminal_code',
        'direction',
        'entity',
        'success',
        'records',
        'message',
        'duration_ms',
    ];

    protected $casts = [
        'success'     => 'bool',
        'records'     => 'int',
        'duration_ms' => 'int',
    ];

    /**
     * Escribe una entrada sin permitir que un fallo de la bitácora tumbe la
     * sincronización en sí.
     */
    public static function record(array $attributes): void
    {
        try {
            static::create($attributes);
        } catch (Throwable $e) {
            // Silencioso a propósito: la bitácora nunca debe romper el flujo.
        }
    }
}
