<?php

namespace Modules\WhatsAppBot\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class BotMessage extends Model
{
    use UsesTenantConnection;

    protected $table = 'bot_messages';

    protected $fillable = [
        'session_id',
        'direction',
        'phone',
        'body',
        'message_id',
        'from_me',
        'raw_payload',
    ];

    protected $casts = [
        'from_me' => 'boolean',
        'raw_payload' => 'array',
    ];

    public function session()
    {
        return $this->belongsTo(BotSession::class, 'session_id');
    }
}
