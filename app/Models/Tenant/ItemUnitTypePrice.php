<?php

namespace App\Models\Tenant;

/**
 * App\Models\Tenant\ItemUnitTypePrice
 *
 * @property int $id
 * @property int $item_unit_type_id
 * @property int $position
 * @property float $price
 * @property string $label
 * @property bool $is_active
 * @property-read \App\Models\Tenant\ItemUnitType $itemUnitType
 * @method static \Illuminate\Database\Eloquent\Builder|ItemUnitTypePrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemUnitTypePrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemUnitTypePrice query()
 * @mixin \Eloquent
 */
class ItemUnitTypePrice extends ModelTenant
{
    public $timestamps = false;

    protected $fillable = [
        'item_unit_type_id',
        'price_label_id',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con ItemUnitType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function itemUnitType()
    {
        return $this->belongsTo(ItemUnitType::class, 'item_unit_type_id');
    }

    /**
     * Relación con PriceLabel
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function priceLabel()
    {
        return $this->belongsTo(PriceLabel::class, 'price_label_id');
    }
}
