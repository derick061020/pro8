<?php

namespace Modules\WhatsAppBot\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class BotSession extends Model
{
    use UsesTenantConnection;

    protected $table = 'bot_sessions';

    protected $fillable = [
        'customer_phone',
        'state',
        'context_json',
        'pending_document_json',
        'last_activity_at',
    ];

    protected $casts = [
        'context_json' => 'array',
        'pending_document_json' => 'array',
        'last_activity_at' => 'datetime',
    ];

    public function messages()
    {
        return $this->hasMany(BotMessage::class, 'session_id');
    }
}
