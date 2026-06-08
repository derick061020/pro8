<?php

namespace Modules\Hotel\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\HotelFloor;
use Modules\Hotel\Models\HotelRent;
use Modules\Hotel\Models\HotelRentChange;
use Modules\Hotel\Models\HotelRentItemPayment;
use Illuminate\Http\Request;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\User;
use Carbon\Carbon;
use DB;

class HotelReceptionController extends Controller
{
	public function index()
	{
		$rooms = $this->getRooms();

		if (request()->ajax()) {
			return response()->json([
				'success' => true,
				'rooms'   => $rooms,
			], 200);
		}
		$floors = HotelFloor::where('active', true)
                ->where('establishment_id',auth()->user()->establishment_id)
				->orderBy('description')
				->get();

		$roomStatus = HotelRoom::$status;

        $userType = auth()->user()->type;
		$establishmentId = auth()->user()->establishment_id;
        $establishments = Establishment::select('id','description')->get();

		return view('hotel::rooms.reception', compact('rooms', 'floors', 'roomStatus','userType','establishmentId','establishments'));
	}

    /**
     * Busqueda avanzada de cuartos.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function  searchRooms(Request $request ){

        $user = auth()->user();
        $rooms = HotelRoom::with('category', 'floor', 'rates')
            ->where('establishment_id', $user->establishment_id)
            ->where('active', true);

        // Si el usuario es limpiador, solo mostrar habitaciones asignadas para limpiar
        if ($user->type === 'limpiador') {
            $roomIdsForCleaning = \App\Models\Tenant\HotelCleaning::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->pluck('hotel_room_id')
                ->toArray();

            $rooms->whereIn('id', $roomIdsForCleaning);
        }

        if ($request->has('hotel_floor_id') && !empty($request->hotel_floor_id)) {
            $rooms->where('hotel_floor_id', $request->hotel_floor_id);
        }
        if ($request->has('hotel_status_room') && !empty($request->hotel_status_room)) {
            $rooms->where('status',  $request->hotel_status_room);
        }
        if ($request->has('hotel_name_room') && !empty($request->hotel_name_room)) {
            $term = trim($request->hotel_name_room);
            // Buscar por nombre de habitación, descripción, o por nombre/numero/
            // documento del cliente del rent activo (real o reserva).
            $rooms->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('description', 'LIKE', "%{$term}%")
                  ->orWhereIn('id', function ($sub) use ($term) {
                      $sub->select('hotel_room_id')
                          ->from('hotel_rents')
                          ->where('status', '!=', 'FINALIZADO')
                          ->where(function ($cq) use ($term) {
                              $cq->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(customer, '$.name')) LIKE ?", ["%{$term}%"])
                                 ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(customer, '$.number')) LIKE ?", ["%{$term}%"])
                                 ->orWhere('license_plate', 'LIKE', "%{$term}%");
                          });
                  });
            });
        }
        $rooms = $rooms->orderBy('name')->get()->each(function ($room) {
            $this->hydrateRoomState($room);
            return $room;
        });

        return response()->json([
            'success' => true,
            'rooms'   => $rooms,
        ], 200);
    }
    /**
     * Devuelve informacion de cuartos disponibles
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|\Modules\Hotel\Models\HotelRoom[]
     */
    private function getRooms()
    {
        $user = auth()->user();
        $rooms = HotelRoom::with('category', 'floor', 'rates', 'establishment')
            ->where('establishment_id', $user->establishment_id)
            ->where('active', true);

        // Si el usuario es limpiador, solo mostrar habitaciones asignadas para limpiar
        if ($user->type === 'limpiador') {
            $roomIdsForCleaning = \App\Models\Tenant\HotelCleaning::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->pluck('hotel_room_id')
                ->toArray();

            $rooms->whereIn('id', $roomIdsForCleaning);
        }

        if (request('hotel_floor_id')) {
            $rooms->where('hotel_floor_id', request('hotel_floor_id'));
        }
        if (request('status')) {
            $rooms->where('status', request('status'));
        }

        $rooms->orderBy('name');
        return $rooms->get()->each(function ($room) {
            $this->hydrateRoomState($room);
            return $room;
        });
    }

    /**
     * Calcula el estado visible de una habitación combinando:
     *  - El rent real (no reserva) activo, si existe.
     *  - Las reservas activas (is_reserve=true, status != FINALIZADO).
     *
     * Reglas:
     *  - Si hay un rent real activo → mantener el status actual (OCUPADO/etc.)
     *    y el rent visible es ese.
     *  - Si NO hay rent real pero hay una reserva cuya ventana
     *    [input_date+time, output_date+time] cubre AHORA → marcar como
     *    `is_active_reservation`. El rent visible es esa reserva.
     *  - En cualquier otro caso → el status real de la habitación
     *    (DISPONIBLE/LIMPIEZA/MANTENIMIENTO) se preserva.
     *
     * Además expone:
     *  - reservations_count: cantidad de reservas activas en la habitación.
     *  - upcoming_reservations: detalle ligero de cada reserva (para el modal).
     */
    private function hydrateRoomState($room)
    {
        $now = \Carbon\Carbon::now();

        // 1. Rent real (no reserva) más reciente y aún no finalizado
        $activeRent = HotelRent::with('items', 'customer.person_type')
            ->where('hotel_room_id', $room->id)
            ->where('status', '!=', 'FINALIZADO')
            ->where(function ($q) {
                $q->where('is_reserve', false)->orWhereNull('is_reserve');
            })
            ->orderBy('id', 'DESC')
            ->first();

        // 2. Todas las reservas activas (no finalizadas) ordenadas por inicio
        $reservations = HotelRent::with('customer.person_type')
            ->where('hotel_room_id', $room->id)
            ->where('status', '!=', 'FINALIZADO')
            ->where('is_reserve', true)
            ->orderBy('input_date', 'ASC')
            ->orderBy('input_time', 'ASC')
            ->get();

        // 3. Reserva cuya ventana cubre AHORA (check-in ya llegó, output no llegó)
        $currentReservation = $reservations->first(function ($r) use ($now) {
            $start = $this->parseDateTimeSafe($r->input_date, $r->input_time ?? '14:00');
            $end   = $this->parseDateTimeSafe($r->output_date, $r->output_time ?? '12:00');
            if (!$start || !$end) return false;
            return $start->lte($now) && $end->gt($now);
        });

        // 4. Resolver rent visible + flags
        if ($activeRent) {
            $this->attachRentBalances($activeRent);
            $room->rent                  = $activeRent;
            $room->has_reservation       = false;
            $room->is_active_reservation = false;
            $room->ready_for_checkin     = false;
        } elseif ($currentReservation) {
            $this->attachRentBalances($currentReservation);
            $room->rent                  = $currentReservation;
            $room->has_reservation       = true;
            $room->is_active_reservation = true;
            $room->ready_for_checkin     = true;
        } else {
            // Validador: si la habitación está OCUPADA pero no tiene rent activo
            // ni reserva vigente, se corrige automáticamente a DISPONIBLE.
            if ($room->status === 'OCUPADO') {
                $room->status = 'DISPONIBLE';
                $room->save();
            }
            $room->rent                  = [];
            $room->has_reservation       = false;
            $room->is_active_reservation = false;
            $room->ready_for_checkin     = false;
        }

        // 5. Contador y listado para el chip + modal
        $room->reservations_count    = $reservations->count();
        $room->upcoming_reservations = $reservations->map(function ($r) use ($now) {
            $start = $this->parseDateTimeSafe($r->input_date, $r->input_time ?? '14:00');
            $end   = $this->parseDateTimeSafe($r->output_date, $r->output_time ?? '12:00');
            $isCurrent = $start && $end && $start->lte($now) && $end->gt($now);
            $isFuture  = $start && $start->gt($now);
            return [
                'id'             => $r->id,
                'customer_name'  => is_object($r->customer) ? ($r->customer->name ?? '—') : '—',
                'input_date'     => $r->input_date,
                'input_time'     => $r->input_time,
                'output_date'    => $r->output_date,
                'output_time'    => $r->output_time,
                'duration'       => $r->duration,
                'status'         => $r->status,
                'is_current'     => $isCurrent,
                'is_future'      => $isFuture,
                'license_plate'  => $r->license_plate,
                'travel_reason'  => $r->travel_reason,
                'notes'          => $r->notes,
            ];
        })->values();

        // 6. Limpieza (igual que antes)
        if ($room->status === 'LIMPIEZA') {
            $cleaning = \App\Models\Tenant\HotelCleaning::where('hotel_room_id', $room->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->first();
            $room->has_cleaner_assigned = $cleaning && $cleaning->user_id !== null;
        } else {
            $room->has_cleaner_assigned = false;
        }
    }

    /**
     * Calcula y adjunta al rent los totales de items, pagos netos y deuda
     * usando la misma fórmula que Checkout.vue (onCalculatePaidAndDebts):
     *
     *   total_debt = Σ items.item.total (excluyendo PAY)
     *              - Σ pagos netos (positivos - devoluciones)
     *              + arrears
     */
    private function attachRentBalances(HotelRent $rent)
    {
        $items = $rent->items ?: collect();

        $totalOriginalItems = $items
            ->filter(function ($i) { return $i->type !== 'PAY'; })
            ->sum(function ($i) {
                $itemObj = $i->item;
                $total = is_object($itemObj) && isset($itemObj->total) ? $itemObj->total : 0;
                return floatval($total);
            });

        $payments = HotelRentItemPayment::whereHas('associated_record_payment', function ($q) use ($rent) {
            $q->where('hotel_rent_id', $rent->id);
        })->get();

        // netPayments = positivos - |negativos| == sum(payment)
        $netPayments = floatval($payments->sum('payment'));
        $totalPositivePayments = floatval($payments->where('payment', '>', 0)->sum('payment'));
        $totalRefunds = floatval($payments->where('payment', '<', 0)->sum(function ($p) {
            return abs(floatval($p->payment));
        }));

        $arrears   = floatval($rent->arrears ?? 0);
        $totalDebt = $totalOriginalItems - $netPayments + $arrears;

        $rent->total_items     = round($totalOriginalItems, 2);
        $rent->total_paid      = round($netPayments, 2);
        $rent->total_payments  = round($totalPositivePayments, 2);
        $rent->total_refunds   = round($totalRefunds, 2);
        $rent->total_debt      = round($totalDebt, 2);
        $rent->saved_payments  = $payments->values();
    }

    /**
     * Parseo defensivo de fecha + hora; devuelve Carbon o null si los datos están mal.
     */
    private function parseDateTimeSafe($date, $time)
    {
        if (!$date) return null;
        $t = $time ?: '00:00';
        // Aceptar HH:MM o HH:MM:SS
        if (strlen($t) === 5) $t .= ':00';
        try {
            return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $t);
        } catch (\Throwable $e) {
            try {
                return \Carbon\Carbon::parse($date . ' ' . $t);
            } catch (\Throwable $e2) {
                return null;
            }
        }
    }

    public function getItem($id)
    {
        $rent = HotelRent::findOrFail($id);

        // Cargar todos los items del rent, no solo los de tipo HAB
        $items = $rent->items;
        
        $item = $items->where('type', 'HAB')->where('payment_status', 'PAID')->first();
        $item_debt = $items->where('type', 'HAB')->where('payment_status', 'DEBT')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'rent' => $rent,
                'items' => $items, // Agregar todos los items
                'item' => $item,
                'item_debt' => $item_debt
            ],
            'message'   => "Datos encontrados",
        ], 200);
    }

    public function changeUserEstablishment(Request $request)
    {
        $user = User::findOrFail(auth()->user()->id);
        $user->establishment_id = $request->establishment_id;
        $user->save();

        return response()->json([
            'success' => true,
            'message'   => "Establecimiento actualizado con éxito",
        ], 200);
    }

    /**
     * Obtener habitaciones disponibles para cambio
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableRooms()
    {
        $rooms = HotelRoom::with('category', 'floor')
            ->where('establishment_id', auth()->user()->establishment_id)
            ->where('status', 'DISPONIBLE')
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'rooms' => $rooms,
            'message' => 'Habitaciones disponibles obtenidas correctamente'
        ], 200);
    }

    /**
     * Editar fechas de check-in/check-out
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editDates($id, Request $request)
    {
        try {
            DB::beginTransaction();

            $rent = HotelRent::with(['room', 'items'])->findOrFail($id);
            
            // Validar que el rent no esté finalizado
            if ($rent->status === 'FINALIZADO') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede editar fechas de un alquiler finalizado'
                ], 400);
            }

            $editInput = $request->input('edit_input', false);
            $editOutput = $request->input('edit_output', false);
            $inputDate = $request->input('input_date');
            $inputTime = $request->input('input_time', '12:00');
            $outputDate = $request->input('output_date');
            $outputTime = $request->input('output_time', '11:59');
            $newPrice = $request->input('new_price');

            // Validar fechas
            $inputDateTime = Carbon::parse("{$inputDate} {$inputTime}");
            $outputDateTime = Carbon::parse("{$outputDate} {$outputTime}");

            if (!$inputDateTime->lt($outputDateTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fecha de salida debe ser posterior a la fecha de ingreso'
                ], 400);
            }

            // Guardar valores originales para el historial
            $oldValues = [
                'input_date' => $rent->input_date,
                'input_time' => $rent->input_time,
                'output_date' => $rent->output_date,
                'output_time' => $rent->output_time
            ];

            // Actualizar fechas
            if ($editInput) {
                $rent->input_date = $inputDate;
                $rent->input_time = $inputTime;
            }
            if ($editOutput) {
                $rent->output_date = $outputDate;
                $rent->output_time = $outputTime;
            }

            // Actualizar precio si se proporciona
            $newItemPrice = null;
            if ($newPrice !== null && $newPrice > 0) {
                $roomItem = $rent->items()->where('type', 'HAB')->first();
                if ($roomItem) {
                    $oldTotal = $roomItem->total;
                    $roomItem->total = $newPrice;
                    
                    // Recalcular unit_price basado en la duración
                    $duration = $inputDateTime->diffInDays($outputDateTime);
                    $unitPrice = $duration > 0 ? $newPrice / $duration : $newPrice;
                    $roomItem->unit_price = $unitPrice;
                    $roomItem->save();

                    $newItemPrice = [
                        'unit_price' => $unitPrice,
                        'total' => $newPrice
                    ];
                }
            }

            $rent->save();

            // Registrar cambio en el historial
            $this->recordChange($rent, 'DATE_EDIT', $oldValues, [
                'input_date' => $editInput ? $inputDate : $rent->input_date,
                'input_time' => $editInput ? $inputTime : $rent->input_time,
                'output_date' => $editOutput ? $outputDate : $rent->output_date,
                'output_time' => $editOutput ? $outputTime : $rent->output_time
            ], $editInput ? 'Edición de fecha de ingreso' : 'Edición de fecha de salida', $request->input('price_difference', 0));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fechas actualizadas correctamente',
                'rent' => $rent->fresh(['room', 'customer']),
                'new_item_price' => $newItemPrice
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar las fechas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener historial de cambios de una habitación
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoomHistory($id)
    {
        try {
            $rent = HotelRent::findOrFail($id);
            
            $history = HotelRentChange::with('user')
                ->where('hotel_rent_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'history' => $history,
                'message' => 'Historial obtenido correctamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el historial: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar un cambio en el historial
     *
     * @param HotelRent $rent
     * @param string $changeType
     * @param array $oldValues
     * @param array $newValues
     * @param string|null $notes
     * @param float $priceDifference
     * @return HotelRentChange
     */
    private function recordChange($rent, $changeType, $oldValues, $newValues, $notes = null, $priceDifference = 0)
    {
        return HotelRentChange::create([
            'hotel_rent_id' => $rent->id,
            'change_type' => $changeType,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'notes' => $notes,
            'price_difference' => $priceDifference,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Obtener datos actualizados de una habitación específica
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoom($id)
    {
        try {
            $user = auth()->user();
            $room = HotelRoom::with('category', 'floor', 'rates', 'establishment')
                ->where('establishment_id', $user->establishment_id)
                ->where('id', $id)
                ->first();

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Habitación no encontrada'
                ], 404);
            }

            // Aplicar la misma lógica que en getRooms() para determinar has_cleaner_assigned
            if ($room->status === 'LIMPIEZA') {
                $cleaning = \App\Models\Tenant\HotelCleaning::where('hotel_room_id', $room->id)
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->first();
                $room->has_cleaner_assigned = $cleaning && $cleaning->user_id !== null;
            } else {
                $room->has_cleaner_assigned = false;
            }

            return response()->json([
                'success' => true,
                'room' => $room
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la habitación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint público para registrar cambios (usado por el frontend)
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordChangePublic($id, Request $request)
    {
        try {
            $rent = HotelRent::findOrFail($id);
            
            $change = $this->recordChange(
                $rent,
                $request->input('change_type'),
                $request->input('old_values', []),
                $request->input('new_values', []),
                $request->input('notes'),
                $request->input('price_difference', 0)
            );

            return response()->json([
                'success' => true,
                'message' => 'Cambio registrado correctamente',
                'change' => $change
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el cambio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina una entrada del historial de cambios SIN deshacer la operación.
     * Solo borra el registro del log (fechas, ítems y cobros quedan intactos).
     *
     * @param int $rentId
     * @param int $changeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteChange($rentId, $changeId)
    {
        try {
            $change = HotelRentChange::where('hotel_rent_id', $rentId)
                ->where('id', $changeId)
                ->firstOrFail();

            $change->delete();

            return response()->json([
                'success' => true,
                'message' => 'Registro del historial eliminado correctamente.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el registro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revierte (deshace) la operación asociada a una entrada del historial:
     *  - EXTENSION   → borra el ítem de extensión y restaura fecha/duración previa.
     *  - PRODUCT_ADD → borra los productos agregados y sus cobros.
     *  - ROOM_CHANGE → regresa el alquiler a la habitación anterior.
     *  - DATE_EDIT   → restaura las fechas anteriores.
     *
     * Bloqueado si algún ítem afectado ya tiene comprobante emitido
     * (document_id / sale_note_id). Tras revertir, la entrada del historial
     * se elimina para evitar reversiones duplicadas.
     *
     * @param int $rentId
     * @param int $changeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function revertChange($rentId, $changeId)
    {
        DB::connection('tenant')->beginTransaction();
        try {
            $rent = HotelRent::findOrFail($rentId);

            if ($rent->status === 'FINALIZADO') {
                DB::connection('tenant')->rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede revertir cambios de un alquiler finalizado.'
                ], 400);
            }

            $change = HotelRentChange::where('hotel_rent_id', $rentId)
                ->where('id', $changeId)
                ->firstOrFail();

            $old = $change->old_values ?: [];
            $new = $change->new_values ?: [];

            switch ($change->change_type) {
                case 'EXTENSION':
                    $this->revertExtension($rent, $old, $new);
                    break;
                case 'PRODUCT_ADD':
                    $this->revertProductAdd($rent, $new);
                    break;
                case 'ROOM_CHANGE':
                    $this->revertRoomChange($rent, $old, $new);
                    break;
                case 'DATE_EDIT':
                    $this->revertDateEdit($rent, $old);
                    break;
                default:
                    DB::connection('tenant')->rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Este tipo de cambio no se puede revertir.'
                    ], 400);
            }

            // Eliminar la entrada del historial tras revertir.
            $change->delete();

            DB::connection('tenant')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Cambio revertido correctamente.'
            ], 200);

        } catch (\Throwable $th) {
            DB::connection('tenant')->rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 422);
        }
    }

    /**
     * Verifica que un ítem no esté facturado; lanza excepción si lo está.
     */
    private function assertItemNotInvoiced($item)
    {
        if ($item && ($item->document_id !== null || $item->sale_note_id !== null)) {
            throw new \Exception('No se puede revertir: el cargo ya tiene un comprobante emitido.');
        }
    }

    private function revertExtension(HotelRent $rent, array $old, array $new)
    {
        $itemId = $new['item_id'] ?? null;
        if ($itemId) {
            $item = \Modules\Hotel\Models\HotelRentItem::where('hotel_rent_id', $rent->id)
                ->where('id', $itemId)
                ->first();
            $this->assertItemNotInvoiced($item);
            if ($item) {
                $item->payments()->delete();
                $item->delete();
            }
        }

        if (array_key_exists('duration', $old))    $rent->duration    = $old['duration'];
        if (array_key_exists('output_date', $old)) $rent->output_date = $old['output_date'];
        if (array_key_exists('output_time', $old)) $rent->output_time = $old['output_time'];
        $rent->save();
    }

    private function revertProductAdd(HotelRent $rent, array $new)
    {
        $itemIds = $new['item_ids'] ?? [];
        if (empty($itemIds) && !empty($new['products'])) {
            $itemIds = array_column($new['products'], 'item_id');
        }

        $items = \Modules\Hotel\Models\HotelRentItem::where('hotel_rent_id', $rent->id)
            ->whereIn('id', $itemIds)
            ->get();

        foreach ($items as $item) {
            $this->assertItemNotInvoiced($item);
        }
        foreach ($items as $item) {
            $item->payments()->delete();
            $item->delete();
        }
    }

    private function revertRoomChange(HotelRent $rent, array $old, array $new)
    {
        $oldItemId = $old['item_id'] ?? null;
        $newItemId = $new['item_id'] ?? null;

        $oldItem = $oldItemId
            ? \Modules\Hotel\Models\HotelRentItem::where('hotel_rent_id', $rent->id)->where('id', $oldItemId)->first()
            : null;
        $newItem = $newItemId
            ? \Modules\Hotel\Models\HotelRentItem::where('hotel_rent_id', $rent->id)->where('id', $newItemId)->first()
            : null;

        $this->assertItemNotInvoiced($oldItem);
        if ($newItem && $newItem->id !== ($oldItem->id ?? null)) {
            $this->assertItemNotInvoiced($newItem);
        }

        // Caso split: el cambio creó un item HAB nuevo distinto del anterior.
        if ($newItem && $oldItem && $newItem->id !== $oldItem->id) {
            $newItem->payments()->delete();
            $newItem->delete();
        }

        // Restaurar el item HAB anterior desde el snapshot.
        $snapshot = $old['item_snapshot'] ?? null;
        if ($oldItem && $snapshot) {
            $oldItem->item_id        = $snapshot['item_id'] ?? $oldItem->item_id;
            $oldItem->item           = $snapshot['item'] ?? $oldItem->item;
            $oldItem->quantity       = $snapshot['quantity'] ?? $oldItem->quantity;
            $oldItem->unit_price     = $snapshot['unit_price'] ?? $oldItem->unit_price;
            $oldItem->total          = $snapshot['total'] ?? $oldItem->total;
            $oldItem->description    = $snapshot['description'] ?? $oldItem->description;
            $oldItem->payment_status = $snapshot['payment_status'] ?? $oldItem->payment_status;
            $oldItem->save();
        }

        // Regresar el alquiler a la habitación/tarifa anterior.
        $oldRoomId = $old['hotel_room_id'] ?? null;
        $newRoomId = $new['hotel_room_id'] ?? null;

        if ($oldRoomId) {
            $rent->hotel_room_id = $oldRoomId;
        }
        if (array_key_exists('hotel_rate_id', $old)) $rent->hotel_rate_id = $old['hotel_rate_id'];
        if (array_key_exists('rental_price', $old))  $rent->rental_price  = $old['rental_price'];
        elseif (array_key_exists('unit_price', $old)) $rent->rental_price = $old['unit_price'];
        $rent->save();

        // Estados de habitaciones: la anterior vuelve a OCUPADO, la nueva queda libre.
        if ($oldRoomId) {
            HotelRoom::where('id', $oldRoomId)->update(['status' => 'OCUPADO']);
        }
        if ($newRoomId && $newRoomId != $oldRoomId) {
            HotelRoom::where('id', $newRoomId)->update(['status' => 'DISPONIBLE']);
        }
    }

    private function revertDateEdit(HotelRent $rent, array $old)
    {
        if (array_key_exists('input_date', $old))  $rent->input_date  = $old['input_date'];
        if (array_key_exists('input_time', $old))  $rent->input_time  = $old['input_time'];
        if (array_key_exists('output_date', $old)) $rent->output_date = $old['output_date'];
        if (array_key_exists('output_time', $old)) $rent->output_time = $old['output_time'];
        $rent->save();
    }
}
