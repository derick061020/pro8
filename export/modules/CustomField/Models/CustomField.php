<?php

namespace Modules\CustomField\Models;

use App\Models\Tenant\ModelTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomField extends ModelTenant
{
    use HasFactory;

    protected $table = 'custom_fields';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'required',
        'options',
        'order',
        'enabled_for_documents',
        'enabled_for_sale_notes',
        'enabled_for_dispatches',
        'enabled_for_order_notes',
    ];

    protected $casts = [
        'required' => 'boolean',
        'options' => 'array',
        'enabled_for_documents' => 'boolean',
        'enabled_for_sale_notes' => 'boolean',
        'enabled_for_dispatches' => 'boolean',
        'enabled_for_order_notes' => 'boolean',
    ];
}
