<?php

namespace Modules\Webhook\Models;

use App\Models\Tenant\ModelTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebhookSubscription extends ModelTenant
{
    const MAX_CONSECUTIVE_FAILURES = 10;

    protected $table = 'webhook_subscriptions';

    protected $fillable = [
        'name',
        'url',
        'secret',
        'events',
        'is_active',
        'consecutive_failures',
        'disabled_at',
    ];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
        'disabled_at' => 'datetime',
    ];

    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }

    public function scopeActiveForEvent(Builder $query, string $event): Builder
    {
        return $query->where('is_active', true)
            ->whereJsonContains('events', $event);
    }

    /**
     * Registra una entrega definitivamente fallida; al acumular
     * MAX_CONSECUTIVE_FAILURES la suscripción se desactiva sola.
     */
    public function registerFailure(): void
    {
        $this->consecutive_failures += 1;

        if ($this->consecutive_failures >= self::MAX_CONSECUTIVE_FAILURES) {
            $this->is_active = false;
            $this->disabled_at = now();
        }

        $this->save();
    }

    public function resetFailures(): void
    {
        if ($this->consecutive_failures > 0) {
            $this->consecutive_failures = 0;
            $this->save();
        }
    }
}
