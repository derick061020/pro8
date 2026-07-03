<?php

namespace Modules\Ecommerce\Models\Tenant;

use App\Models\Tenant\ModelTenant;
use Hyn\Tenancy\Traits\UsesTenantConnection;

/**
 * Zona de delivery del ecommerce.
 * Una zona agrupa un conjunto de ubicaciones con un mismo precio de envío.
 *
 * @property int    $id
 * @property string $name
 * @property float  $price
 * @property bool   $specify_zone
 * @property bool   $active
 */
class DeliveryZone extends ModelTenant
{
    use UsesTenantConnection;

    protected $table = 'delivery_zones';

    protected $fillable = [
        'name',
        'price',
        'specify_zone',
        'active',
    ];

    protected $casts = [
        'price'        => 'float',
        'specify_zone' => 'boolean',
        'active'       => 'boolean',
    ];

    /**
     * Ubicaciones (departamento / provincia / distrito) que cubre esta zona.
     */
    public function locations()
    {
        return $this->hasMany(DeliveryZoneLocation::class, 'delivery_zone_id');
    }

    /**
     * Scope para filtrar sólo zonas activas.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Datos de colección para la API y el datatable.
     */
    public function getCollectionData(): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'price'        => $this->price,
            'price_formatted' => 'S/ ' . number_format($this->price, 2),
            'specify_zone' => $this->specify_zone,
            'active'       => $this->active,
            'locations'    => $this->locations->map(fn ($loc) => $loc->getCollectionData())->values()->toArray(),
            'created_at'   => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
