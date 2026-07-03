<?php

namespace Modules\FullSuscription\Models\Tenant;

use App\Models\Tenant\ModelTenant;

class SuscriptionPaymentReminder extends ModelTenant
{
    const SHIPPING_MEDIUM_EMAIL    = 'email';
    const SHIPPING_MEDIUM_WHATSAPP = 'whatsapp';

    const REMINDER_TYPE_BEFORE   = 'before';
    const REMINDER_TYPE_SAME_DAY = 'same_day';
    const REMINDER_TYPE_AFTER    = 'after';

    public $timestamps = false;

    protected $fillable = [
        'reminder_days',
        'reminder_type',
        'reminder_time',
        'shipping_medium',
    ];

    protected $casts = [
        'reminder_time' => 'datetime',
    ];
}
