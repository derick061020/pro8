<?php

namespace Modules\Hotel\Models;

use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\Establishment;
use Carbon\Carbon;

/**
 * Periodo de mantenimiento programado de una habitación.
 *
 * Se crea desde el calendario de reservas (opción "Poner en mantenimiento" al
 * seleccionar días). El estado físico de la habitación (hotel_rooms.status) se
 * sincroniza de forma perezosa con estos periodos mediante reconcile():
 *  - Cuando llega la fecha de inicio → la habitación pasa a MANTENIMIENTO.
 *  - Cuando termina el rango          → vuelve a DISPONIBLE.
 */
class HotelRoomMaintenance extends ModelTenant
{
    protected $table = 'hotel_room_maintenances';

    protected $fillable = [
        'hotel_room_id',
        'establishment_id',
        'start_date',
        'end_date',
        'reason',
        'status',
    ];

    public function room()
    {
        return $this->belongsTo(HotelRoom::class, 'hotel_room_id');
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class)->select('id', 'description');
    }

    /**
     * Sincroniza el estado de las habitaciones con sus periodos de mantenimiento.
     *
     * Se llama al cargar el calendario y la recepción, de modo que el estado se
     * actualiza solo (sin necesidad de un cron): en cuanto se abre cualquier
     * pantalla del módulo, las habitaciones cuyo mantenimiento ya empezó quedan
     * en MANTENIMIENTO y las que ya terminaron vuelven a DISPONIBLE.
     *
     * @param int|null $establishmentId  Limitar a una sucursal (opcional).
     */
    public static function reconcile($establishmentId = null)
    {
        $today = Carbon::today();

        $query = self::where('status', '!=', 'DONE');
        if ($establishmentId) {
            $query->where('establishment_id', $establishmentId);
        }

        foreach ($query->get() as $maintenance) {
            $room = HotelRoom::find($maintenance->hotel_room_id);
            if (!$room) {
                $maintenance->status = 'DONE';
                $maintenance->save();
                continue;
            }

            $start = Carbon::parse($maintenance->start_date)->startOfDay();
            $end   = Carbon::parse($maintenance->end_date)->startOfDay();

            if ($end->lt($today)) {
                // El periodo ya terminó: cerrarlo y devolver la habitación a
                // disponible si sigue en mantenimiento y no hay otro periodo
                // vigente encima.
                $maintenance->status = 'DONE';
                $maintenance->save();

                if ($room->status === 'MANTENIMIENTO'
                    && !self::hasActivePeriod($room->id, $today, $maintenance->id)) {
                    $room->status = 'DISPONIBLE';
                    $room->save();
                }
            } elseif ($start->lte($today) && $end->gte($today)) {
                // El periodo está en curso: marcar la habitación en
                // mantenimiento (sin pisar una habitación ocupada).
                if ($maintenance->status !== 'ACTIVE') {
                    $maintenance->status = 'ACTIVE';
                    $maintenance->save();
                }
                if (!in_array($room->status, ['MANTENIMIENTO', 'OCUPADO'], true)) {
                    $room->status = 'MANTENIMIENTO';
                    $room->save();
                }
            }
            // Periodo futuro (start > hoy): se deja SCHEDULED sin tocar la habitación.
        }
    }

    /**
     * ¿Existe otro periodo de mantenimiento (distinto de $excludeId) que cubra
     * la fecha dada y siga vigente?
     */
    private static function hasActivePeriod($roomId, $today, $excludeId)
    {
        return self::where('hotel_room_id', $roomId)
            ->where('id', '!=', $excludeId)
            ->where('status', '!=', 'DONE')
            ->whereDate('start_date', '<=', $today->toDateString())
            ->whereDate('end_date', '>=', $today->toDateString())
            ->exists();
    }
}
