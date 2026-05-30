<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Catalogs\AffectationIgvType;
use App\Models\Tenant\Catalogs\CurrencyType;
use App\Models\Tenant\Catalogs\SystemIscType;
use App\Models\Tenant\Catalogs\UnitType;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\Tenant\ItemUnitType
 *
 * @property-read \App\Models\Tenant\Item $item
 * @property-read UnitType $unit_type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant\ItemUnitTypePrice[] $prices
 * @method static \Illuminate\Database\Eloquent\Builder|ItemUnitType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemUnitType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemUnitType query()
 * @mixin \Eloquent
 */
class ItemUnitType extends ModelTenant
{
     protected $with = ['unit_type'];
    public $timestamps = false;

    protected $fillable = [
        'description',
        'item_id',
        'unit_type_id',
        'quantity_unit',
        'price1',
        'price1_name',
        'price2',
        'price2_name',
        'price3',
        'price3_name',
        'price_default',
        'barcode'
    ];

    protected static function booted()
    {
        static::created(function (self $itemUnitType) {
            $id = $itemUnitType->item_id;
            Cache::tags(['item_detail'])->forget("item_detail_{$id}");
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit_type() {
        return $this->belongsTo(UnitType::class, 'unit_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item() {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relación con precios dinámicos (ordenados por position del priceLabel)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prices()
    {
        return $this->hasMany(ItemUnitTypePrice::class, 'item_unit_type_id')
            ->join('price_labels', 'item_unit_type_prices.price_label_id', '=', 'price_labels.id')
            ->orderBy('price_labels.position')
            ->select('item_unit_type_prices.*')
            ;
    }


    /**
     * Retorna un standar de nomenclatura para el modelo
     *
     * @param int $decimal_units
     *
     * @return array
     */
    public function getCollectionData($decimal_units = 2){
        return [
            'id'            => $this->id,
            'description'   => "{$this->description}",
            'item_id'       => $this->item_id,
            'unit_type_id'  => $this->unit_type_id,
            'quantity_unit' => number_format($this->quantity_unit, $decimal_units, '.', ''),
            'price_default' => $this->price_default,
            'barcode'       => $this->barcode,
            'prices'        => PriceLabel::all()->map(function($price_label) use ($decimal_units) {
                $price = $this->prices->firstWhere('price_label_id', $price_label->id);
                return [
                    'id'             => $price ? $price->id : null,
                    'price_label_id' => $price_label->id,
                    'position'       => $price_label->position,
                    'label'          => $price_label->label,
                    'price'          => $price ? number_format($price->price, $decimal_units, '.', '') : 0,
                    'is_active'      => $price ? (bool) $price->is_active : false,
                ];
            })->toArray(),
        ];
    }

}
