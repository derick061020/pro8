<?php

namespace Modules\Offline\Models;

use App\Models\Tenant\ModelTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Un cambio local pendiente de subir al servidor online.
 *
 * @property string $uuid
 * @property string $entity
 * @property int    $entity_id
 * @property string $operation
 * @property array  $payload
 * @property string $status
 */
class SyncQueueItem extends ModelTenant
{
    public const STATUS_PENDING  = 'pending';
    public const STATUS_SENDING  = 'sending';
    public const STATUS_SYNCED   = 'synced';
    public const STATUS_FAILED   = 'failed';
    public const STATUS_CONFLICT = 'conflict';

    /** Tras este número de intentos el elemento deja de reintentarse solo. */
    public const MAX_ATTEMPTS = 8;

    protected $table = 'offline_sync_queue';

    protected $fillable = [
        'uuid',
        'terminal_code',
        'entity',
        'entity_id',
        'operation',
        'payload',
        'depends_on',
        'priority',
        'status',
        'attempts',
        'last_error',
        'next_attempt_at',
        'remote_id',
        'synced_at',
    ];

    protected $casts = [
        'payload'         => 'array',
        'depends_on'      => 'array',
        'attempts'        => 'int',
        'priority'        => 'int',
        'next_attempt_at' => 'datetime',
        'synced_at'       => 'datetime',
    ];

    /**
     * Elementos listos para enviarse: pendientes o fallidos cuya espera venció.
     */
    public function scopeReadyToSend(Builder $query): Builder
    {
        return $query
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_FAILED])
            ->where('attempts', '<', self::MAX_ATTEMPTS)
            ->where(function (Builder $q) {
                $q->whereNull('next_attempt_at')
                  ->orWhere('next_attempt_at', '<=', now());
            })
            ->orderBy('priority')
            ->orderBy('id');
    }

    /**
     * Requieren intervención: agotaron reintentos o el servidor los rechazó.
     */
    public function scopeStuck(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('status', self::STATUS_CONFLICT)
              ->orWhere(function (Builder $q2) {
                  $q2->where('status', self::STATUS_FAILED)
                     ->where('attempts', '>=', self::MAX_ATTEMPTS);
              });
        });
    }

    public function markSynced(?int $remoteId): void
    {
        $this->update([
            'status'     => self::STATUS_SYNCED,
            'remote_id'  => $remoteId,
            'last_error' => null,
            'synced_at'  => now(),
        ]);
    }

    /**
     * Marca el fallo y programa el próximo intento con espera creciente
     * (1, 2, 4, 8... minutos, con techo de 30) para no golpear al servidor.
     */
    public function markFailed(string $message, bool $conflict = false): void
    {
        $attempts = $this->attempts + 1;
        $delay    = min(30, 2 ** max(0, $attempts - 1));

        $this->update([
            'status'          => $conflict ? self::STATUS_CONFLICT : self::STATUS_FAILED,
            'attempts'        => $attempts,
            'last_error'      => mb_substr($message, 0, 2000),
            'next_attempt_at' => $conflict ? null : Carbon::now()->addMinutes($delay),
        ]);
    }
}
