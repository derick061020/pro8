<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant\User;
use Hyn\Tenancy\Traits\UsesTenantConnection;

/**
 * Un mensaje del hilo de observaciones de una estadía.
 *
 * Sustituye al campo único `hotel_rents.notes`, que se sobrescribía en cada
 * edición y hacía imposible saber quién había anotado qué ni cuándo.
 */
class HotelRentNote extends Model
{
    use UsesTenantConnection;

    protected $table = 'hotel_rent_notes';

    protected $fillable = [
        'hotel_rent_id',
        'user_id',
        'user_name',
        'body',
    ];

    public function rent()
    {
        return $this->belongsTo(HotelRent::class, 'hotel_rent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Autor a mostrar: el nombre congelado al escribir, o el del usuario si la
     * nota es anterior a que se guardara el nombre.
     */
    public function getAuthorAttribute()
    {
        return $this->user_name ?: (optional($this->user)->name ?: 'Sistema');
    }
}
