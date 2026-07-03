<?php

namespace Modules\Restaurant\Models;

use App\Models\Tenant\ModelTenant;

class Printer extends ModelTenant
{
    protected $fillable = [
        'name',
        'is_default',
        'active',
        'last_seen_at',
    ];

    protected $casts = [
        'is_default'   => 'boolean',
        'active'       => 'boolean',
        'last_seen_at' => 'datetime',
    ];
}
