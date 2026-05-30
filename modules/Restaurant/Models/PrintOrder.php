<?php

namespace Modules\Restaurant\Models;

use App\Models\Tenant\ModelTenant;

class PrintOrder extends ModelTenant
{
    protected $fillable = [
        'name_printer',
        'status',
        'pdf_b64',
    ];

    /**
     * status:
     * 0 = pendiente
     * 1 = procesando
     * 2 = impresa
     */
    protected $casts = [
        'status' => 'integer',
    ];

}
