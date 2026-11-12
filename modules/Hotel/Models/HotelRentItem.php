<?php

namespace Modules\Hotel\Models;

use App\Models\Tenant\ModelTenant;

class HotelRentItem extends ModelTenant
{
	protected $table = 'hotel_rent_items';

	protected $fillable = [
		'type',
		'hotel_rent_id',
		'item_id',
		'item',
		'payment_status',
		'hotel_rent_order_id',
		'sale_note_id',
		'document_id',
		'invoiced_at',
		'quantity',
		'unit_price',
		'total',
		'description',
	];

	protected $casts = [
		'invoiced_at' => 'datetime',
		'quantity' => 'decimal:4',
		'unit_price' => 'decimal:4',
		'total' => 'decimal:4',
	];

	public function getItemAttribute($value)
	{
		return (is_null($value)) ? null : (object) json_decode($value);
	}

	public function setItemAttribute($value)
	{
		$this->attributes['item'] = (is_null($value)) ? null : json_encode($value);
	}

    public function payments()
    {
        return $this->hasOne(HotelRentItemPayment::class, 'hotel_rent_item_id');
    }

	public function hotel_rent()
	{
		return $this->belongsTo(HotelRent::class);
	}


	/**
	 * Validar si se encuentra pagado
	 *
	 * @return bool
	 */
	public function isPaid()
	{
		return $this->payment_status === 'PAID';
	}

	
	/**
	 * 
	 * Descripcion dependiendo del tipo, habitacion o producto
	 *
	 * @return string
	 */
	public function getDescriptionFromType()
	{
		return $this->type == 'HAB' ? 'Renta habitación' : 'Venta producto';
	}

	
    /**
     *
     * Obtener relaciones necesarias o aplicar filtros para reporte pagos
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeFilterRelationsGlobalPayment($query)
    {
        return $query->with([
			'hotel_rent' => function ($q) {
				$q->select([
					'id',
					'customer_id',
					'customer',
					'payment_status',
					'input_date'
				]);
			},
		]);
    }


    /** Sufijo de duración que se agrega al nombre: " x 3 noche(s)", " x 2 hora(s)", " x 1 mes(es)" */
    public const DURATION_SUFFIX_REGEX = '/\s*x\s*\d+(?:\.\d+)?\s*(?:noche\(s\)|hora\(s\)|mes\(es\))/iu';

    /**
     * Nombre en PDF con el sufijo de duración en la misma línea.
     *
     * El nombre en PDF del producto suele venir en HTML del editor
     * ("<p>Habitación 33</p>"); si el sufijo se pega al final queda fuera del
     * párrafo ("<p>Habitación 33</p> x 1 noche(s)") y la nota de venta lo
     * muestra en dos líneas. Aquí se quita cualquier sufijo anterior y se pone
     * el nuevo dentro del último párrafo. Sin $suffix, se conserva el que ya
     * tenía (sólo se corrige su posición).
     */
    public static function pdfNameWithSuffix($pdfName, ?string $suffix = null): string
    {
        $pdfName = (string) $pdfName;
        if ($suffix === null) {
            if (!preg_match_all(self::DURATION_SUFFIX_REGEX, $pdfName, $found)) {
                return $pdfName;
            }
            $suffix = ' '.trim(end($found[0]));
        }

        $clean = rtrim(preg_replace(self::DURATION_SUFFIX_REGEX, '', $pdfName));
        if (preg_match('/<\/p>$/i', $clean)) {
            return preg_replace('/(?:\s|&nbsp;)*<\/p>$/i', $suffix.'</p>', $clean);
        }

        return $clean.$suffix;
    }
}
