<?php

namespace Modules\DocumentFormLayout\Models;

use App\Models\Tenant\ModelTenant;

class DocumentFormLayout extends ModelTenant
{
    protected $table = 'document_form_layouts';

    protected $fillable = [
        'variant',
        'pinned_fields',
        'updated_by_user_id',
    ];

    protected $casts = [
        'pinned_fields' => 'array',
    ];

    public const ALLOWED_VARIANTS = ['invoice'];
}
