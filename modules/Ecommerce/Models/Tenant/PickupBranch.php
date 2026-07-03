<?php

namespace Modules\Ecommerce\Models\Tenant;

use App\Models\Tenant\ModelTenant;

/**
 * Modelo para las sucursales de recojo en tienda del módulo Ecommerce.
 * Permite al cliente elegir un punto de recojo como alternativa al delivery.
 */
class PickupBranch extends ModelTenant
{
    protected $table = 'ecommerce_pickup_branches';

    protected $fillable = [
        'name',
        'address',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Scope para obtener solo las sucursales activas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Retorna los campos públicos para el frontend
     */
    public function getCollectionData(): array
    {
        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'address' => $this->address,
            'active'  => $this->active,
        ];
    }
}
