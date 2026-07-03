<?php

namespace App\Models\System;

use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\Model;

class PublicSearchCustomization extends Model
{
    use UsesSystemConnection;

    protected $fillable = [
        'slug',
        'background_color',
        'background_image_path',
    ];
}
