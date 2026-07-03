<?php

namespace App\Models\System;

use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\Model;

class Skin extends Model
{
    use UsesSystemConnection;

    protected $table = 'system_skins';

    protected $fillable = [
        'name',
        'filename',
        'custom_filename',
        'is_default',
        'is_tenant_default',
        'is_forced',
        'is_visible_to_clients',
    ];

    public function getCollectionData()
    {
        return [
            'id'                      => $this->id,
            'name'                    => $this->name,
            'filename'                => $this->filename,
            'active_filename'         => $this->custom_filename ?? $this->filename,
            'is_default'              => (bool) $this->is_default,
            'is_replaced'             => !is_null($this->custom_filename),
            'is_tenant_default'       => (bool) $this->is_tenant_default,
            'is_forced'               => (bool) $this->is_forced,
            'is_visible_to_clients'   => (bool) ($this->is_visible_to_clients ?? true),
        ];
    }
}
