<?php

namespace App\Models\Tenant;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class WhatsappMessageLog extends Model
{
    use UsesTenantConnection;

    protected $table = 'whatsapp_message_logs';

    protected $fillable = [
        'phone',
        'filename',
        'status',
    ];
}
