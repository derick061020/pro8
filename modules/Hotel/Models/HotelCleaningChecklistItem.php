<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant\User;
use App\Models\Tenant\HotelCleaning;
use Hyn\Tenancy\Traits\UsesTenantConnection;

/**
 * Una casilla del checklist de una limpieza.
 *
 * El checklist tiene dos secciones con artículos DISTINTOS: lo que SE PUSO
 * (consumibles que se reponen) y lo que SE CAMBIÓ (ropa de cama y baño que se
 * sustituye). Recepción marca al asignar qué hay que hacer, y la encargada va
 * marcando lo hecho; cada casilla guarda la hora exacta en que se completó.
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

    /**
     * Definición del checklist: cada sección con SUS artículos, en el orden en
     * que se muestran. Es la única fuente: para añadir o quitar un artículo se
     * toca aquí (el Vue tiene una copia sólo para el formulario de asignación).
     */
    const CHECKLIST = [
        'placed' => [
            'label' => 'Se puso',
            'items' => [
                'toilet_paper' => 'Papel higiénico',
                'soap'         => 'Jabón',
                'sheets'       => 'Sábanas',
            ],
        ],
        'changed' => [
            'label' => 'Se cambió',
            'items' => [
                'towels'  => 'Toallas',
                'duvets'  => 'Edredones',
                'pillows' => 'Cojines',
            ],
        ],
    ];

    /** Todas las casillas posibles como "seccion:articulo". */
    public static function validKeys()
    {
        $keys = [];

        foreach (self::CHECKLIST as $section => $definition) {
            foreach (array_keys($definition['items']) as $itemKey) {
                $keys[] = $section . ':' . $itemKey;
            }
        }

        return $keys;
    }

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
        return self::CHECKLIST[$this->section]['label'] ?? $this->section;
    }

    public function getItemLabelAttribute()
    {
        return self::CHECKLIST[$this->section]['items'][$this->item_key] ?? $this->item_key;
    }
}
