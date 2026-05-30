<?php

namespace Modules\Hotel\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\HotelCleaning;
use App\Models\Tenant\HotelRoom;
use App\Models\Tenant\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HotelCleaningController extends Controller
{
    /**
     * Obtener limpiadores disponibles
     */
    public function getCleaners()
    {
        $cleaners = DB::connection('tenant')->table('users')
            ->where('type', 'limpiador')
            ->where('active', true)
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
                    // Verificar si hay un rent activo
                    $activeRent = \Modules\Hotel\Models\HotelRent::where('hotel_room_id', $room->id)
                        ->where('status', '!=', 'FINALIZADO')
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
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una limpieza en progreso para esta habitación'
                ], 400);
            }

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
}
