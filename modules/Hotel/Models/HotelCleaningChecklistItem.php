<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant\User;
use App\Models\Tenant\HotelCleaning;
use Hyn\Tenancy\Traits\UsesTenantConnection;

/**
 * Una casilla del checklist de una limpieza.
 *
 * El checklist es la misma lista de artículos repetida en dos secciones: lo que
 * SE PUSO (se repuso lo que faltaba) y lo que SE CAMBIÓ (se sustituyó lo usado).
 * Recepción marca al asignar qué hay que hacer, y la encargada va marcando lo
 * hecho; cada casilla guarda la hora exacta en que se completó.
 */
class HotelCleaningChecklistItem extends Model
{
    use UsesTenantConnection;

    protected $table = 'hotel_cleaning_checklist_items';

    protected $fillable = [
        'hotel_cleaning_id',
        'section',
        'item_key',
        'planned',
        'done',
        'done_at',
        'done_by',
    ];

    protected $casts = [
        'planned' => 'boolean',
        'done'    => 'boolean',
        'done_at' => 'datetime',
    ];

    /** Secciones del checklist, en el orden en que se muestran. */
    const SECTIONS = [
        'placed'  => 'Se puso',
        'changed' => 'Se cambió',
    ];

    /** Artículos del checklist, en el orden en que se muestran. */
    const ITEMS = [
        'towels'       => 'Toallas',
        'toilet_paper' => 'Papel higiénico',
        'soap'         => 'Jabón',
        'sheets'       => 'Sábanas',
        'duvets'       => 'Edredones',
        'pillows'      => 'Cojines',
    ];

    public function cleaning()
    {
        return $this->belongsTo(HotelCleaning::class, 'hotel_cleaning_id');
    }

    public function doneByUser()
    {
        return $this->belongsTo(User::class, 'done_by');
    }

    public function getSectionLabelAttribute()
    {
        return self::SECTIONS[$this->section] ?? $this->section;
    }

    public function getItemLabelAttribute()
    {
        return self::ITEMS[$this->item_key] ?? $this->item_key;
    }
}
