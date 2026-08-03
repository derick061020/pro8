<?php

namespace Modules\Offline\Models;

use App\Models\Tenant\ModelTenant;

/**
 * Hasta dónde bajó el terminal cada entidad maestra desde el servidor.
 *
 * @property string $entity
 * @property \Illuminate\Support\Carbon|null $last_synced_at
 */
class PullState extends ModelTenant
{
    protected $table = 'offline_pull_states';

    protected $fillable = ['entity', 'last_synced_at', 'last_remote_id', 'records'];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'last_remote_id' => 'int',
        'records'        => 'int',
    ];

    public static function forEntity(string $entity): self
    {
        return static::firstOrCreate(['entity' => $entity]);
    }
}
