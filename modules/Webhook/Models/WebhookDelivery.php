<?php

namespace Modules\Webhook\Models;

use App\Models\Tenant\ModelTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class WebhookDelivery extends ModelTenant
{
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    const RESPONSE_BODY_LIMIT = 2000;

    protected $table = 'webhook_deliveries';

    protected $fillable = [
        'webhook_subscription_id',
        'event',
        'event_id',
        'payload',
        'status',
        'attempts',
        'response_code',
        'response_body',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(WebhookSubscription::class, 'webhook_subscription_id');
    }

    public function markSuccess(int $code, ?string $body): void
    {
        $this->update([
            'status' => self::STATUS_SUCCESS,
            'response_code' => $code,
            'response_body' => $body !== null ? Str::limit($body, self::RESPONSE_BODY_LIMIT) : null,
            'error_message' => null,
            'sent_at' => now(),
        ]);
    }

    public function markFailed(?int $code, ?string $error): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'response_code' => $code,
            'error_message' => $error !== null ? Str::limit($error, self::RESPONSE_BODY_LIMIT) : null,
            'sent_at' => now(),
        ]);
    }
}
