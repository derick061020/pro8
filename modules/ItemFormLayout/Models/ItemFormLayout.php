<?php

namespace Modules\ItemFormLayout\Models;

use App\Models\Tenant\ModelTenant;

class ItemFormLayout extends ModelTenant
{
    protected $table = 'item_form_layouts';

    protected $fillable = [
        'variant',
        'pinned_fields',
        'updated_by_user_id',
    ];

    protected $casts = [
        'pinned_fields' => 'array',
    ];

    public const ALLOWED_VARIANTS = ['standard', 'ecommerce', 'restaurant'];
}
