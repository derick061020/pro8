<?php

namespace App\Models\System;

use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\Model;

class ColumnVisibilityConfig extends Model
{
    use UsesSystemConnection;

    protected $fillable = ['module', 'columns'];

    protected $casts = ['columns' => 'array'];
}
