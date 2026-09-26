<?php

namespace Modules\Hotel\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\HotelCleaning;
use App\Models\Tenant\HotelRoom;
use App\Models\Tenant\User;
use App\Models\Tenant\Company;
use App\Models\Tenant\Establishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Hotel\Exports\HotelCleaningExport;
use Modules\Hotel\Models\HotelCleaningChecklistItem;
use Carbon\Carbon;

class HotelCleaningController extends Controller
{
    /**
     * Obtener limpiadores disponibles.
     *
     * Solo los de la sucursal actual: recepción trabaja siempre sobre
     * `auth()->user()->establishment_id` (el mismo valor que cambia
     * HotelReceptionController::changeUserEstablishment), así que el selector
     * de limpiadores no debe ofrecer personal de otras sucursales.
     */
    public function getCleaners()
    {
        $establishmentId = auth()->user()->establishment_id;

        $cleaners = DB::connection('tenant')->table('users')
            ->where('type', 'limpiador')
            ->where('active', true)
            ->where('establishment_id', $establishmentId)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'cleaners' => $cleaners
        ]);
    }

    /**
     * Iniciar limpieza rápida
     */
    public function startQuickCleaning(Request $request)
    {
        $request->validate([
            'room_id' => 'required|integer|min:1',
            'cleaner_id' => 'required|integer|min:1'
        ]);

        try {
            DB::connection('tenant')->beginTransaction();

            // Verificar que el limpiador sea de tipo limpiador
            $cleaner = DB::connection('tenant')->table('users')
                ->where('id', $request->cleaner_id)
                ->first();
            if (!$cleaner || $cleaner->type !== 'limpiador') {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario seleccionado no es un limpiador'
                ], 400);
            }

            // Verificar que la habitación esté disponible (usando conexión tenant)
            $room = \Modules\Hotel\Models\HotelRoom::where('id', $request->room_id)->first();
            if (!$room || $room->status == 'MANTENIMIENTO') {
                return response()->json([
                    'success' => false,
                    'message' => 'La habitación no está disponible para limpieza'
                ], 400);
            }

            // Verificar si ya hay una limpieza en progreso para esta habitación
            $existingCleaning = DB::connection('tenant')->table('hotel_cleanings')
                ->where('hotel_room_id', $request->room_id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->first();

            if ($existingCleaning) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una limpieza en progreso para esta habitación'
                ], 400);
            }

            // Crear registro de limpieza
            $cleaning = DB::connection('tenant')->table('hotel_cleanings')->insertGetId([
                'hotel_room_id' => $request->room_id,
                'user_id' => $request->cleaner_id,
                'status' => 'in_progress',
                'start_time' => now(),
                'notes' => $request->input('notes', 'Limpieza rápida iniciada'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Checklist de lo que hay que hacer, tal como lo dejó marcado quien
            // asigna la limpieza (por defecto, todo).
            $this->seedChecklist($cleaning, $this->normalizeChecklistSelection($request->input('checklist')));

            // Actualizar estado de la habitación
            $room->status = 'LIMPIEZA';
            $room->save();

            DB::connection('tenant')->commit();

            // Obtener el registro completo para respuesta
            $cleaningResponse = DB::connection('tenant')->table('hotel_cleanings')
                ->where('id', $cleaning)
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Limpieza iniciada exitosamente',
                'cleaning' => $cleaningResponse
            ]);

        } catch (\Throwable $th) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar la limpieza: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Finalizar limpieza
     */
    public function completeCleaning($id)
    {
        try {
            DB::connection('tenant')->beginTransaction();

            // Obtener la limpieza
            $cleaning = DB::connection('tenant')->table('hotel_cleanings')
                ->where('id', $id)
                ->first();

            if (!$cleaning) {
                return response()->json([
                    'success' => false,
                    'message' => 'Limpieza no encontrada'
                ], 404);
            }

            if ($cleaning->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'La limpieza ya fue completada'
                ], 400);
            }

            // Marcar limpieza como completada
            DB::connection('tenant')->table('hotel_cleanings')
                ->where('id', $id)
                ->update([
                    'status' => 'completed',
                    'end_time' => now(),
                    'updated_at' => now()
                ]);

            // Actualizar estado de la habitación solo si estaba en LIMPIEZA (después de checkout)
            // Si está OCUPADA, no cambiar el estado (era una limpieza rápida)
            $room = \Modules\Hotel\Models\HotelRoom::find($cleaning->hotel_room_id);
            if ($room) {
                if ($room->status === 'LIMPIEZA') {
                    // Verificar si hay una RENTA activa (huésped dentro). Las
                    // reservas (is_reserve = true) no cuentan: nadie ocupa aún la
                    // habitación, y marcarla OCUPADO por una reserva pendiente
                    // bloqueaba luego su propio check-in.
                    $activeRent = \Modules\Hotel\Models\HotelRent::where('hotel_room_id', $room->id)
                        ->where('status', '!=', 'FINALIZADO')
                        ->where(function ($q) {
                            $q->where('is_reserve', false)->orWhereNull('is_reserve');
                        })
                        ->first();

                    if ($activeRent) {
                        // Hay rent activo, cambiar a OCUPADO para que el huésped pueda usarla
                        $room->status = 'OCUPADO';
                        $room->save();
                    } else {
                        // No hay rent activo, se puede marcar como disponible
                        $room->status = 'DISPONIBLE';
                        $room->save();
                    }
                }
                // Si está OCUPADA, no hacer nada (era una limpieza rápida)
            }

            DB::connection('tenant')->commit();

            // Obtener el registro actualizado para respuesta
            $updatedCleaning = DB::connection('tenant')->table('hotel_cleanings')
                ->where('id', $id)
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Limpieza completada exitosamente',
                'cleaning' => $updatedCleaning
            ]);

        } catch (\Throwable $th) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al completar la limpieza: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener limpiezas activas
     */
    public function getActiveCleanings()
    {
        $cleanings = DB::connection('tenant')->table('hotel_cleanings')
            ->join('users', 'users.id', '=', 'hotel_cleanings.user_id')
            ->join('hotel_rooms', 'hotel_rooms.id', '=', 'hotel_cleanings.hotel_room_id')
            ->select('hotel_cleanings.*', 'users.name as cleaner_name', 'users.email as cleaner_email', 'hotel_rooms.name as room_name')
            ->whereIn('hotel_cleanings.status', ['pending', 'in_progress'])
            ->orderBy('hotel_cleanings.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'cleanings' => $cleanings
        ]);
    }

    /**
     * Obtener historial de limpiezas de una habitación
     */
    public function getRoomCleaningHistory($roomId)
    {
        $cleanings = DB::connection('tenant')->table('hotel_cleanings')
            ->join('users', 'users.id', '=', 'hotel_cleanings.user_id')
            ->select('hotel_cleanings.*', 'users.name as cleaner_name', 'users.email as cleaner_email')
            ->where('hotel_cleanings.hotel_room_id', $roomId)
            ->orderBy('hotel_cleanings.created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'cleanings' => $cleanings
        ]);
    }

    /**
     * Asignar limpiador y comenzar limpieza para habitación en LIMPIEZA
     */
    public function assignCleanerAndStart(Request $request)
    {
        $request->validate([
            'room_id' => 'required|integer|min:1',
            'cleaner_id' => 'required|integer|min:1'
        ]);

        try {
            DB::connection('tenant')->beginTransaction();

            // Verificar que el limpiador sea de tipo limpiador
            $cleaner = DB::connection('tenant')->table('users')
                ->where('id', $request->cleaner_id)
                ->first();
            if (!$cleaner || $cleaner->type !== 'limpiador') {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario seleccionado no es un limpiador'
                ], 400);
            }

            // Verificar que la habitación esté en estado LIMPIEZA
            $room = \Modules\Hotel\Models\HotelRoom::where('id', $request->room_id)->first();
            if (!$room || $room->status !== 'LIMPIEZA') {
                return response()->json([
                    'success' => false,
                    'message' => 'La habitación no está en estado LIMPIEZA'
                ], 400);
            }

            // Verificar si ya hay una limpieza activa para esta habitación
            $existingCleaning = DB::connection('tenant')->table('hotel_cleanings')
                ->where('hotel_room_id', $request->room_id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->first();

            if ($existingCleaning) {
                // Si la limpieza está pendiente y no tiene limpiador, asignar al actual
                if ($existingCleaning->user_id === null) {
                    DB::connection('tenant')->table('hotel_cleanings')
                        ->where('id', $existingCleaning->id)
                        ->update([
                            'user_id'   => $request->cleaner_id,
                            'status'    => 'in_progress',
                            'notes'     => $request->input('notes', $existingCleaning->notes ?? 'Limpieza asignada desde recepción'),
                            'updated_at'=> now(),
                        ]);
                    $cleaning = $existingCleaning->id;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Esta habitación ya tiene un limpiador asignado'
                    ], 400);
                }
            } else {
                // Crear registro de limpieza completo con limpiador asignado
                $cleaning = DB::connection('tenant')->table('hotel_cleanings')->insertGetId([
                    'hotel_room_id' => $request->room_id,
                    'user_id' => $request->cleaner_id,
                    'status' => 'in_progress',
                    'start_time' => now(),
                    'notes' => $request->input('notes', 'Limpieza asignada desde recepción'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Checklist de lo que hay que hacer. Si la limpieza ya existía (la
            // que se crea sola al hacer checkout) puede que ya lo tenga: el
            // sembrado no pisa lo que haya.
            $this->seedChecklist($cleaning, $this->normalizeChecklistSelection($request->input('checklist')));

            // No cambiar el estado aquí, la habitación permanece en LIMPIEZA
            // hasta que se complete la limpieza

            DB::connection('tenant')->commit();

            // Obtener el registro completo para respuesta
            $cleaningResponse = DB::connection('tenant')->table('hotel_cleanings')
                ->where('id', $cleaning)
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Limpiador asignado y limpieza iniciada',
                'cleaning' => $cleaningResponse
            ]);

        } catch (\Throwable $th) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar limpiador: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Checklist de una limpieza, agrupado por sección.
     *
     * Si la limpieza no tiene checklist (por ejemplo, la que se crea sola al
     * hacer checkout), se siembra con todo marcado como pendiente por hacer:
     * es el comportamiento por defecto que pidió recepción.
     */
    public function cleaningChecklist($cleaningId)
    {
        try {
            $cleaning = HotelCleaning::findOrFail($cleaningId);

            $this->seedChecklist($cleaning->id);

            return response()->json([
                'success'   => true,
                'checklist' => $this->checklistPayload($cleaning->id),
                'sections'  => HotelCleaningChecklistItem::SECTIONS,
                'items'     => HotelCleaningChecklistItem::ITEMS,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo cargar el checklist: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Marca o desmarca una casilla.
     *
     * Se guarda en el momento de marcarla, no al cerrar la limpieza: así queda
     * la hora real en que la encargada terminó cada cosa, y no todas con la
     * misma hora del botón final.
     */
    public function toggleChecklistItem($cleaningId, $itemId, Request $request)
    {
        try {
            $cleaning = HotelCleaning::findOrFail($cleaningId);

            if ($cleaning->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'La limpieza ya fue completada: el checklist queda como registro.'
                ], 400);
            }

            $item = HotelCleaningChecklistItem::where('hotel_cleaning_id', $cleaning->id)
                ->findOrFail($itemId);

            $done = $request->boolean('done');

            $item->done    = $done;
            $item->done_at = $done ? now() : null;
            $item->done_by = $done ? auth()->id() : null;
            $item->save();

            return response()->json([
                'success'   => true,
                'checklist' => $this->checklistPayload($cleaning->id),
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar el checklist: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Crea las casillas de una limpieza.
     *
     * `$planned` es la lista de "seccion:articulo" que recepción dejó marcada al
     * asignar. Si no llega nada, se planifica todo (el valor por defecto del
     * formulario es con todo marcado). Se ejecuta una sola vez por limpieza.
     */
    private function seedChecklist($cleaningId, array $planned = null)
    {
        if (HotelCleaningChecklistItem::where('hotel_cleaning_id', $cleaningId)->exists()) {
            return;
        }

        $rows = [];

        foreach (array_keys(HotelCleaningChecklistItem::SECTIONS) as $section) {
            foreach (array_keys(HotelCleaningChecklistItem::ITEMS) as $itemKey) {
                $rows[] = [
                    'hotel_cleaning_id' => $cleaningId,
                    'section'           => $section,
                    'item_key'          => $itemKey,
                    // Sin selección explícita se planifica todo.
                    'planned'           => $planned === null || in_array($section . ':' . $itemKey, $planned, true),
                    'done'              => false,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            }
        }

        HotelCleaningChecklistItem::insert($rows);
    }

    /**
     * Normaliza la selección que manda el formulario a "seccion:articulo",
     * descartando cualquier valor que no exista.
     */
    private function normalizeChecklistSelection($raw)
    {
        if (!is_array($raw)) {
            return null;
        }

        $valid = [];

        foreach (array_keys(HotelCleaningChecklistItem::SECTIONS) as $section) {
            foreach (array_keys(HotelCleaningChecklistItem::ITEMS) as $itemKey) {
                $valid[] = $section . ':' . $itemKey;
            }
        }

        return array_values(array_intersect($raw, $valid));
    }

    /**
     * El checklist listo para pintar: agrupado por sección y con la hora de
     * cada casilla completada.
     */
    private function checklistPayload($cleaningId)
    {
        $items = HotelCleaningChecklistItem::with('doneByUser:id,name')
            ->where('hotel_cleaning_id', $cleaningId)
            ->get()
            ->keyBy(fn ($i) => $i->section . ':' . $i->item_key);

        $payload = [];

        foreach (HotelCleaningChecklistItem::SECTIONS as $section => $sectionLabel) {
            $rows = [];

            foreach (HotelCleaningChecklistItem::ITEMS as $itemKey => $itemLabel) {
                $item = $items->get($section . ':' . $itemKey);

                if (!$item) {
                    continue;
                }

                $rows[] = [
                    'id'         => $item->id,
                    'item_key'   => $itemKey,
                    'label'      => $itemLabel,
                    'planned'    => (bool) $item->planned,
                    'done'       => (bool) $item->done,
                    'done_at'    => optional($item->done_at)->format('Y-m-d H:i:s'),
                    'done_time'  => optional($item->done_at)->format('H:i'),
                    'done_by'    => optional($item->doneByUser)->name,
                ];
            }

            $payload[] = [
                'section' => $section,
                'label'   => $sectionLabel,
                'items'   => $rows,
            ];
        }

        return $payload;
    }

    /**
     * Obtener limpiezas asignadas a un limpiador
     */
    public function getCleanerAssignments($cleanerId)
    {
        $cleanings = DB::connection('tenant')->table('hotel_cleanings')
            ->join('hotel_rooms', 'hotel_rooms.id', '=', 'hotel_cleanings.hotel_room_id')
            ->select('hotel_cleanings.*', 'hotel_rooms.name as room_name', 'hotel_rooms.status as room_status')
            ->where('hotel_cleanings.user_id', $cleanerId)
            ->whereIn('hotel_cleanings.status', ['pending', 'in_progress'])
            ->orderBy('hotel_cleanings.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'cleanings' => $cleanings
        ]);
    }

    /**
     * Reporte de limpiezas en Excel (mismo formato que el reporte de recepción)
     */
    public function report($start, $end, $establishment_id)
    {
        $user = auth()->user();
        $establishment = $user->establishment;

        $query = DB::connection('tenant')->table('hotel_cleanings')
            ->join('hotel_rooms', 'hotel_rooms.id', '=', 'hotel_cleanings.hotel_room_id')
            ->leftJoin('users', 'users.id', '=', 'hotel_cleanings.user_id')
            ->leftJoin('hotel_categories', 'hotel_categories.id', '=', 'hotel_rooms.hotel_category_id')
            ->whereBetween(DB::raw('DATE(hotel_cleanings.created_at)'), [$start, $end])
            ->select(
                'hotel_cleanings.*',
                'hotel_rooms.name as room_name',
                'hotel_rooms.establishment_id as establishment_id',
                'hotel_categories.description as category',
                'users.name as cleaner_name'
            );

        if ($establishment_id && $user->type === 'admin') {
            $query->where('hotel_rooms.establishment_id', $establishment_id);
            $establishment = Establishment::findOrFail($establishment_id);
        }

        if ($user->type != 'admin') {
            $query->where('hotel_rooms.establishment_id', $user->establishment_id);
        }

        $cleanings = $query->orderBy('hotel_cleanings.created_at', 'desc')->get();

        $statusLabels = [
            'pending'     => 'Pendiente',
            'in_progress' => 'En progreso',
            'completed'   => 'Completada',
        ];

        $records = collect($cleanings)->transform(function ($row) use ($statusLabels) {
            $duration = '';
            if ($row->start_time && $row->end_time) {
                $duration = Carbon::parse($row->start_time)->diffInMinutes(Carbon::parse($row->end_time));
            }

            return [
                'id'           => $row->id,
                'room_name'    => $row->room_name,
                'category'     => $row->category,
                'cleaner_name' => $row->cleaner_name,
                'status'       => $statusLabels[$row->status] ?? $row->status,
                'start_time'   => $row->start_time,
                'end_time'     => $row->end_time,
                'duration'     => $duration,
                'notes'        => $row->notes,
            ];
        });

        $filename = "Reporte_Limpieza";
        $company = Company::first();

        return (new HotelCleaningExport)
            ->records($records)
            ->company($company)
            ->establishment($establishment)
            ->download($filename . Carbon::now() . '.xlsx');
    }
}
