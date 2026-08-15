<?php

namespace Modules\Hotel\Models;

use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\Establishment;

class HotelRate extends ModelTenant
{
	protected $table = 'hotel_rates';

	protected $fillable = [
		'description',
		'active',
		'establishment_id',
	];

	public function getActiveAttribute($value)
	{
		return $value ? true : false;
	}

	/**
	 * Traduce el id de tarifa que llega desde el front al id real del catálogo
	 * (`hotel_rates.id`), que es al que apunta la FK `hotel_rents.hotel_rate_id`.
	 *
	 * Algunas pantallas trabajan con las tarifas ASIGNADAS a la habitación
	 * (filas de `hotel_room_rates`, con su propio id y su precio) y enviaban ese
	 * id: al guardarlo en el alquiler reventaba la FK con un error SQL crudo.
	 * Aquí se acepta cualquiera de los dos y se devuelve siempre el del catálogo,
	 * o null si el id no corresponde a ninguna tarifa (en ese caso el llamador
	 * debe dejar la tarifa vigente sin tocar en lugar de fallar).
	 *
	 * @param  mixed    $rateId
	 * @param  int|null $roomId
	 * @return int|null
	 */
	public static function resolveCatalogId($rateId, $roomId = null)
	{
		$rateId = (int) $rateId;

		if ($rateId <= 0) return null;

		if (self::where('id', $rateId)->exists()) return $rateId;

		$roomRate = HotelRoomRate::where('id', $rateId)
			->when($roomId, function ($query) use ($roomId) {
				$query->where('hotel_room_id', $roomId);
			})
			->first();

		if ($roomRate && self::where('id', $roomRate->hotel_rate_id)->exists()) {
			return (int) $roomRate->hotel_rate_id;
		}

		return null;
	}

	public function establishment()
    {
        return $this->belongsTo(Establishment::class)->select('id', 'description');
    }
}
