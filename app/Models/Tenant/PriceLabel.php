<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class PriceLabel
 *
 * Gestión de etiquetas de precios globales configurables
 * Centraliza los labels que se usan en todos los productos
 *
 * @property int $id
 * @property int $position
 * @property string $label
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|ItemUnitTypePrice[] $itemUnitTypePrices
 *
 * @method static Builder|PriceLabel active()
 * @method static Builder|PriceLabel ordered()
 */
class PriceLabel extends ModelTenant
{
    protected $fillable = [
        'position',
        'label',
        'is_active',
    ];

    protected $casts = [
        'position' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con los precios de presentaciones de items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function itemUnitTypePrices()
    {
        return $this->hasMany(ItemUnitTypePrice::class, 'price_label_id');
    }

    /**
     * Scope: solo labels activos
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: ordenados por position
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc');
    }

    /**
     * Verifica si el label es uno de los 3 originales (no eliminable)
     *
     * @return bool
     */
    public function isOriginal()
    {
        return in_array($this->id, [1, 2, 3]);
    }

    /**
     * Estructura para API Resource
     *
     * @return array
     */
    public function getCollectionData()
    {
        return [
            'id' => $this->id,
            'position' => $this->position,
            'label' => $this->label,
            'is_active' => (bool) $this->is_active,
            // 'is_original' => $this->isOriginal(),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
