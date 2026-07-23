<?php

namespace App\Models\Tenant;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Hyn\Tenancy\Traits\UsesTenantConnection;

/**
 * Class SeriesDeviceGroup
 *
 * Grupo de series dedicadas vinculable a un unico equipo (por nombre).
 *
 * @property int         $id
 * @property int         $establishment_id
 * @property string      $name
 * @property string|null $module_value
 * @property string|null $bound_device_name
 * @property int|null    $bound_user_id
 * @property Carbon|null $bound_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Tenant
 */
class SeriesDeviceGroup extends ModelTenant
{
    use UsesTenantConnection;

    protected $table = 'series_device_groups';

    protected $fillable = [
        'establishment_id',
        'name',
        'module_value',
        'bound_device_name',
        'bound_user_id',
        'bound_at',
    ];

    protected $casts = [
        'bound_at' => 'datetime',
    ];

    /**
     * Catalogo de modulos asignables a un grupo. Extensible: agregar entradas aqui.
     * value = modules.value ; label = etiqueta para la UI.
     *
     * @var array<string, string>
     */
    public const ASSIGNABLE_MODULES = [
        'restaurant_app' => 'Restaurante',
        'ecommerce'      => 'Ecommerce',
        'hotels'         => 'Hoteles',
    ];

    /**
     * @return BelongsTo
     */
    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    /**
     * @return BelongsTo
     */
    public function bound_user()
    {
        return $this->belongsTo(User::class, 'bound_user_id');
    }

    /**
     * Series dedicadas que pertenecen al grupo.
     *
     * @return HasMany
     */
    public function series()
    {
        return $this->hasMany(Series::class, 'series_device_group_id');
    }

    /**
     * Vinculo activo a un equipo.
     *
     * @return bool
     */
    public function getIsBoundAttribute(): bool
    {
        return !empty($this->bound_device_name);
    }

    /**
     * Etiqueta del modulo asociado (o null).
     *
     * @return string|null
     */
    public function getModuleLabelAttribute(): ?string
    {
        return self::ASSIGNABLE_MODULES[$this->module_value] ?? null;
    }

    /**
     * Grupos vinculados (en uso por algun equipo).
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeBound(Builder $query): Builder
    {
        return $query->whereNotNull('bound_device_name');
    }

    /**
     * Grupos disponibles para un equipo dado: sin vincular o vinculados a ESE equipo (mismo nombre).
     *
     * @param Builder     $query
     * @param string|null $device_name
     * @return Builder
     */
    public function scopeAvailableForDevice(Builder $query, ?string $device_name): Builder
    {
        return $query->where(function (Builder $q) use ($device_name) {
            $q->whereNull('bound_device_name');
            if (!empty($device_name)) {
                $q->orWhere('bound_device_name', $device_name);
            }
        });
    }

    /**
     * Grupo vinculado a un equipo concreto (por nombre).
     *
     * @param Builder $query
     * @param string  $device_name
     * @return Builder
     */
    public function scopeForDevice(Builder $query, string $device_name): Builder
    {
        return $query->where('bound_device_name', $device_name);
    }

    /**
     * Vincular el grupo a un equipo.
     *
     * @param string   $device_name
     * @param int|null $user_id
     * @return void
     */
    public function bindToDevice(string $device_name, ?int $user_id = null): void
    {
        $this->bound_device_name = $device_name;
        $this->bound_user_id = $user_id;
        $this->bound_at = now();
        $this->save();
    }

    /**
     * Desvincular el grupo del equipo.
     *
     * @return void
     */
    public function unbind(): void
    {
        $this->bound_device_name = null;
        $this->bound_user_id = null;
        $this->bound_at = null;
        $this->save();
    }
}
