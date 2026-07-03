<?php

namespace Modules\Ecommerce\Models\Tenant;

use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\Catalogs\Department;
use App\Models\Tenant\Catalogs\Province;
use App\Models\Tenant\Catalogs\District;
use Hyn\Tenancy\Traits\UsesTenantConnection;

/**
 * Ubicación asociada a una zona de delivery.
 * Puede representar cobertura a nivel de:
 *  - Departamento: department_id definido, province_id y district_id nulos.
 *  - Departamento + Provincia: province_id definido, district_id nulo.
 *  - Exacta: los tres campos definidos.
 *
 * @property int    $id
 * @property int    $delivery_zone_id
 * @property string $department_id
 * @property string|null $province_id
 * @property string|null $district_id
 */
class DeliveryZoneLocation extends ModelTenant
{
    use UsesTenantConnection;

    protected $table = 'delivery_zone_locations';

    protected $fillable = [
        'delivery_zone_id',
        'department_id',
        'province_id',
        'district_id',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function zone()
    {
        return $this->belongsTo(DeliveryZone::class, 'delivery_zone_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Construye la etiqueta legible para esta ubicación:
     * Ej: "Lima", "Lima > Lima", "Lima > Lima > San Isidro"
     */
    public function getLabelAttribute(): string
    {
        $parts = [];

        if ($this->department) {
            $parts[] = $this->department->description;
        }
        if ($this->province) {
            $parts[] = $this->province->description;
        }
        if ($this->district) {
            $parts[] = $this->district->description;
        }

        return implode(' > ', $parts);
    }

    /**
     * Datos para la API y los chips del datatable.
     */
    public function getCollectionData(): array
    {
        return [
            'id'            => $this->id,
            'department_id' => $this->department_id,
            'province_id'   => $this->province_id,
            'district_id'   => $this->district_id,
            'label'         => $this->label,
        ];
    }
}
