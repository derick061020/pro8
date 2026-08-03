<?php

namespace Modules\Offline\Models;

use App\Models\Tenant\Establishment;
use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\User;

/**
 * Terminal Windows dado de alta en el servidor online.
 *
 * @property string $code
 * @property string $name
 * @property bool   $active
 */
class Terminal extends ModelTenant
{
    protected $table = 'offline_terminals';

    protected $fillable = [
        'code',
        'name',
        'establishment_id',
        'user_id',
        'active',
        'app_version',
        'last_ip',
        'last_seen_at',
        'last_push_at',
        'last_pull_at',
        'pending_hint',
    ];

    protected $casts = [
        'active'       => 'bool',
        'last_seen_at' => 'datetime',
        'last_push_at' => 'datetime',
        'last_pull_at' => 'datetime',
        'pending_hint' => 'int',
    ];

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Minutos desde el último contacto. null si nunca se conectó.
     */
    public function minutesSinceLastSeen(): ?int
    {
        return $this->last_seen_at ? $this->last_seen_at->diffInMinutes(now()) : null;
    }
}
