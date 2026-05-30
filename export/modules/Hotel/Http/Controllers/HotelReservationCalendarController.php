<?php

namespace Modules\Hotel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Hotel\Models\HotelRent;
use Modules\Hotel\Models\HotelRentItem;
use Modules\Hotel\Models\HotelRentOrder;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\HotelCategory;
use Modules\Hotel\Http\Requests\HotelReservationRequest;
use App\Models\Tenant\Person;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\Series;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Catalogs\AffectationIgvType;
use Modules\Finance\Traits\FinanceTrait;
use Carbon\Carbon;

class HotelReservationCalendarController extends Controller
{
    use FinanceTrait;
    public function index()
    {
        return view('hotel::reservations.calendar');
    }

    public function getCalendarEvents(Request $request)
    {
        $roomId    = $request->get('room_id');
        $status    = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        $query = HotelRent::with(['room', 'room.category', 'items']);

        if ($roomId) {
            $query->where('hotel_room_id', $roomId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Filtro de solapamiento estándar: reserva.input <= rango.fin AND reserva.output >= rango.inicio
        if ($startDate && $endDate) {
            $query->where('input_date', '<=', $endDate)
                  ->where('output_date', '>=', $startDate);
        }

        $reservations = $query->orderBy('input_date', 'asc')->get();

        $events = $reservations->map(function ($reservation) {
            $inputAt  = Carbon::parse($reservation->input_date);
            $outputAt = Carbon::parse($reservation->output_date);

            $customerData      = $reservation->customer;
            $customerName      = (is_object($customerData) && isset($customerData->name)) ? $customerData->name : 'N/A';
            $customerAddress   = (is_object($customerData) && isset($customerData->address)) ? $customerData->address : null;
            $customerTelephone = (is_object($customerData) && isset($customerData->telephone)) ? $customerData->telephone : null;
            $customerNumber    = (is_object($customerData) && isset($customerData->number)) ? $customerData->number : null;

            $roomName     = $reservation->room ? $reservation->room->name : 'Habitación eliminada';
            $roomCategory = ($reservation->room && $reservation->room->category) ? $reservation->room->category->description : 'N/A';

            return [
                'id'                 => $reservation->id,
                'title'              => $roomName . ' - ' . $customerName,
                'start'              => $inputAt->format('Y-m-d'),
                'end'                => $outputAt->format('Y-m-d'),
                'start_date'         => $inputAt->format('Y-m-d'),
                'end_date'           => $outputAt->format('Y-m-d'),
                'input_time'         => $reservation->input_time,
                'output_time'        => $reservation->output_time,
                'customer_id'        => $reservation->customer_id,
                'customer_name'      => $customerName,
                'customer_address'   => $customerAddress,
                'customer_telephone' => $customerTelephone,
                'customer_number'    => $customerNumber,
                'room_name'          => $roomName,
                'room_category'      => $roomCategory,
                'hotel_room_id'      => $reservation->hotel_room_id,
                'hotel_rate_id'      => $reservation->hotel_rate_id,
                'status'             => $reservation->status,
                'duration'           => $reservation->duration,
                'total'              => $this->computeReservationTotal($reservation),
                'rental_price'       => (float) $reservation->rental_price,
                'rental_period_type' => $reservation->rental_period_type,
                'adults'             => $reservation->adults,
                'children'           => $reservation->children,
                'quantity_persons'   => $reservation->quantity_persons,
                'towels'             => $reservation->towels,
                'license_plate'      => $reservation->license_plate,
                'travel_reason'      => $reservation->travel_reason,
                'notes'              => $reservation->notes,
                'is_reserve'         => (bool) $reservation->is_reserve,
                'created_at'         => optional($reservation->created_at)->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json(['data' => $events]);
    }

    /**
     * Suma del total de los items de un alquiler.
     * Acepta items con la columna `total` o con `total` dentro del JSON `item`
     * (formato antiguo previo a la columna plana).
     */
    private function computeReservationTotal(HotelRent $reservation)
    {
        if (!$reservation->relationLoaded('items')) {
            $reservation->load('items');
        }
        return (float) $reservation->items->sum(function ($item) {
            $col = (float) $item->total;
            if ($col > 0) return $col;
            $json = is_object($item->item) ? (array) $item->item : ($item->item ?: []);
            return (float) ($json['total'] ?? 0);
        });
    }

    public function getRooms()
    {
        $rooms = HotelRoom::with('category')
            ->where('active', true)
            ->get()
            ->map(function ($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'category' => $room->category ? $room->category->description : 'N/A',
                    'status' => $room->status,
                ];
            });

        return response()->json([
            'data' => $rooms
        ]);
    }

    public function getReservationDetails($id)
    {
        try {
            $reservation = HotelRent::with([
                'room.category',
                'room.rates.rate',
                'rate',
                'items',
            ])->findOrFail($id);
        } catch (\Illuminate\Database\QueryException $e) {
            // Si hay error de tabla no encontrada, intentar sin la relación room
            if (strpos($e->getMessage(), 'Base table or view not found') !== false) {
                $reservation = HotelRent::with([
                    'rate',
                    'items',
                ])->findOrFail($id);
            } else {
                throw $e;
            }
        }

        $customerData    = $reservation->customer;
        $customerDetails = [
            'id'                        => is_object($customerData) ? ($customerData->id ?? null) : null,
            'name'                      => is_object($customerData) ? ($customerData->name ?? 'N/A') : 'N/A',
            'address'                   => is_object($customerData) ? ($customerData->address ?? null) : null,
            'telephone'                 => is_object($customerData) ? ($customerData->telephone ?? null) : null,
            'email'                     => is_object($customerData) ? ($customerData->email ?? null) : null,
            'number'                    => is_object($customerData) ? ($customerData->number ?? null) : null,
            'identity_document_type_id' => is_object($customerData) ? ($customerData->identity_document_type_id ?? null) : null,
        ];

        $room        = isset($reservation->room) ? $reservation->room : null;
        $roomDetails = [
            'id'                => $room ? $room->id : $reservation->hotel_room_id,
            'name'              => $room ? $room->name : 'Habitación #' . $reservation->hotel_room_id,
            'category'          => ($room && $room->category) ? $room->category->description : 'N/A',
            'description'       => $room ? $room->description : null,
            'hotel_category_id' => $room ? $room->hotel_category_id : null,
            'rates'             => ($room && isset($room->rates)) ? $room->rates->map(function ($rr) {
                return [
                    'hotel_rate_id'    => $rr->hotel_rate_id,
                    'rate_description' => $rr->rate ? $rr->rate->description : null,
                    'price'            => (float) $rr->price,
                ];
            })->values() : [],
        ];

        $itemsOut = $reservation->items->map(function ($item) {
            $json = is_object($item->item) ? (array) $item->item : ($item->item ?: []);
            $unit = (float) $item->unit_price > 0 ? (float) $item->unit_price : (float) ($json['unit_price'] ?? $json['unit_price_value'] ?? 0);
            $tot  = (float) $item->total > 0 ? (float) $item->total : (float) ($json['total'] ?? 0);
            $desc = $item->description ?: ($json['description'] ?? null);
            return [
                'id'             => $item->id,
                'type'           => $item->type,
                'description'    => $desc,
                'quantity'       => (float) $item->quantity > 0 ? (float) $item->quantity : (float) ($json['quantity'] ?? 0),
                'unit_price'     => $unit,
                'total'          => $tot,
                'payment_status' => $item->payment_status,
            ];
        })->values();

        $details = [
            'id'                  => $reservation->id,
            'customer'            => $customerDetails,
            'room'                => $roomDetails,
            'dates'               => [
                'input_date'  => $reservation->input_date,
                'output_date' => $reservation->output_date,
                'input_time'  => $reservation->input_time,
                'output_time' => $reservation->output_time,
                'duration'    => $reservation->duration,
            ],
            'rate'                => [
                'hotel_rate_id'    => $reservation->hotel_rate_id,
                'rate_description' => $reservation->rate ? $reservation->rate->description : null,
                'rental_price'     => (float) $reservation->rental_price,
            ],
            'totals'              => [
                'total' => $this->computeReservationTotal($reservation),
            ],
            'status'              => $reservation->status,
            'adults'              => $reservation->adults,
            'children'            => $reservation->children,
            'quantity_persons'    => $reservation->quantity_persons,
            'towels'              => $reservation->towels,
            'license_plate'       => $reservation->license_plate,
            'travel_reason'       => $reservation->travel_reason,
            'notes'               => $reservation->notes,
            'is_reserve'          => (bool) $reservation->is_reserve,
            'rental_period_type'  => $reservation->rental_period_type,
            'items'               => $itemsOut,
            'created_at'          => optional($reservation->created_at)->format('Y-m-d H:i:s'),
            'updated_at'          => optional($reservation->updated_at)->format('Y-m-d H:i:s'),
        ];

        return response()->json(['data' => $details]);
    }

    public function updateReservationStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:confirmed,pending,cancelled,checked_in,checked_out'
        ]);

        $reservation = HotelRent::findOrFail($id);
        $reservation->status = $request->get('status');
        $reservation->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado de reserva actualizado correctamente'
        ]);
    }

    public function getReservationsByDateRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $startDate = Carbon::parse($request->get('start_date'));
        $endDate = Carbon::parse($request->get('end_date'));

        $reservations = HotelRent::with(['customer', 'room', 'room.category'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('input_date', [$startDate, $endDate])
                      ->orWhereBetween('output_date', [$startDate, $endDate])
                      ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                          $subQuery->where('input_date', '<=', $startDate)
                                   ->where('output_date', '>=', $endDate);
                      });
            })
            ->get();

        return response()->json([
            'data' => $reservations
        ]);
    }

    /**
     * Datos necesarios para el formulario de reserva (mismo patrón que Rent)
     */
    public function getFormTables()
    {
        $customers = Person::whereType('customers')
            ->orderBy('name')
            ->take(20)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'description' => $row->number . ' - ' . $row->name,
                    'name' => $row->name,
                    'number' => $row->number,
                    'identity_document_type_id' => $row->identity_document_type_id,
                    'address' => $row->address,
                    'email' => $row->email,
                    'telephone' => $row->telephone,
                ];
            });

        $payment_method_types = PaymentMethodType::all();
        $payment_destinations = $this->getPaymentDestinations();
        $configuration = Configuration::first();
        $affectation_igv_types = AffectationIgvType::whereActive()->get();
        $series = Series::where('establishment_id', auth()->user()->establishment_id)->get();

        return response()->json([
            'customers' => $customers,
            'payment_method_types' => $payment_method_types,
            'payment_destinations' => $payment_destinations,
            'configuration' => $configuration,
            'affectation_igv_types' => $affectation_igv_types,
            'series' => $series,
        ]);
    }

    /**
     * Buscar clientes (remote search)
     */
    public function searchCustomers(Request $request)
    {
        $input = $request->input('input');
        $customers = Person::whereType('customers')
            ->where(function ($query) use ($input) {
                $query->where('name', 'like', "%{$input}%")
                      ->orWhere('number', 'like', "%{$input}%");
            })
            ->orderBy('name')
            ->take(20)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'description' => $row->number . ' - ' . $row->name,
                    'name' => $row->name,
                    'number' => $row->number,
                    'identity_document_type_id' => $row->identity_document_type_id,
                    'address' => $row->address,
                    'email' => $row->email,
                    'telephone' => $row->telephone,
                ];
            });

        return response()->json(['customers' => $customers]);
    }

    /**
     * Obtener datos de una habitación con tarifas para el formulario
     */
    public function getRoomForForm($roomId)
    {
        $room = HotelRoom::with('category', 'rates.rate')
            ->findOrFail($roomId);

        return response()->json(['data' => $room]);
    }

    /**
     * Crear nueva reserva desde el calendario
     */
    public function storeReservation(HotelReservationRequest $request)
    {
        DB::connection('tenant')->beginTransaction();
        try {
            $roomId = $request->hotel_room_id;
            $room = HotelRoom::findOrFail($roomId);

            if ($room->status === 'MANTENIMIENTO') {
                DB::connection('tenant')->rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'La habitación está en mantenimiento y no puede recibir reservas.',
                ], 422);
            }

            // Verificar solapamiento con precisión de fecha+hora
            $newStart = Carbon::parse($request->input_date . ' ' . ($request->input_time ?: '14:00'));
            $newEnd   = Carbon::parse($request->output_date . ' ' . ($request->output_time ?: '12:00'));

            $conflict = HotelRent::findOverlappingRent($roomId, $newStart, $newEnd);
            if ($conflict) {
                DB::connection('tenant')->rollBack();
                $cStart = Carbon::parse($conflict->input_date . ' ' . ($conflict->input_time ?: '14:00'))->format('d/m/Y H:i');
                $cEnd   = Carbon::parse($conflict->output_date . ' ' . ($conflict->output_time ?: '12:00'))->format('d/m/Y H:i');
                return response()->json([
                    'success' => false,
                    'message' => "La habitación ya está reservada del {$cStart} al {$cEnd}.",
                ], 422);
            }

            $data = $request->only(
                'customer_id', 'customer', 'notes', 'license_plate', 'travel_reason',
                'adults', 'children', 'towels', 'hotel_room_id', 'hotel_rate_id',
                'duration', 'quantity_persons', 'payment_status', 'output_date',
                'output_time', 'input_date', 'input_time', 'data_persons'
            );
            $data['is_reserve'] = true;
            $data['status'] = 'ACTIVE';
            $data['establishment_id'] = $room->establishment_id;

            $rent = HotelRent::create($data);

            // Crear orden
            $order = new HotelRentOrder();
            $order->hotel_rent_id = $rent->id;
            $order->order_number = 1;
            $order->order_status = $request->payment_status;
            $order->sale_note_id = $request->sale_note_id;
            $order->establishment_id = $room->establishment_id;
            $order->save();

            // Crear item de habitación si se envió producto
            if ($request->has('product') && $request->product) {
                $item = new HotelRentItem();
                $item->type = 'HAB';
                $item->hotel_rent_id = $rent->id;
                $item->item_id = $request->product['item_id'];
                $item->item = $request->product;
                $item->payment_status = $request->payment_status;
                $item->hotel_rent_order_id = $order->id;
                $item->save();

                // Registrar pago si aplica
                if ($request->payment_status === 'PAID' && $request->rent_payment) {
                    $this->saveReservationPayment($request->rent_payment, $item);
                }
            }

            DB::connection('tenant')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Reserva creada correctamente.',
                'data' => $rent,
            ]);
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la reserva: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar reserva existente.
     *
     * Validación liviana — pensada para edición inline desde el calendario
     * (no requiere el flujo completo de creación de rent). Solo se actualizan
     * los campos enviados; los demás se preservan.
     */
    public function updateReservation(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id'                        => 'nullable|integer',
            'customer'                           => 'nullable|array',
            'customer.id'                        => 'nullable|integer',
            'customer.name'                      => 'nullable|string|max:255',
            'customer.address'                   => 'nullable|string|max:255',
            'customer.telephone'                 => 'nullable|string|max:50',
            'customer.email'                     => 'nullable|email|max:255',
            'customer.number'                    => 'nullable|string|max:50',
            'customer.identity_document_type_id' => 'nullable',
            'notes'              => 'nullable|string|max:500',
            'license_plate'      => 'nullable|string|max:20',
            'travel_reason'      => 'nullable|in:visita,trabajo,estudio,religion,salud,compras,otros',
            'adults'             => 'nullable|integer|min:0',
            'children'           => 'nullable|integer|min:0',
            'towels'             => 'nullable|integer|min:0',
            'hotel_room_id'      => 'nullable|integer',
            'hotel_rate_id'      => 'nullable|integer',
            'duration'           => 'nullable|integer|min:1',
            'quantity_persons'   => 'nullable|integer|min:1',
            'input_date'         => 'nullable|date_format:Y-m-d',
            'input_time'         => 'nullable|date_format:H:i',
            'output_date'        => 'nullable|date_format:Y-m-d',
            'output_time'        => 'nullable|date_format:H:i',
            'status'             => 'nullable|string|max:30',
        ]);

        DB::connection('tenant')->beginTransaction();
        try {
            $rent = HotelRent::findOrFail($id);

            if ($rent->status === 'FINALIZADO') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede editar una reserva finalizada.',
                ], 422);
            }

            // Resolver valores finales (campo enviado o valor actual)
            $newRoomId    = $validated['hotel_room_id'] ?? $rent->hotel_room_id;
            $newInputDate = $validated['input_date']    ?? $rent->input_date;
            $newInputTime = $validated['input_time']    ?? ($rent->input_time ?: '14:00');
            $newOutputDate= $validated['output_date']   ?? $rent->output_date;
            $newOutputTime= $validated['output_time']   ?? ($rent->output_time ?: '12:00');

            // Habitación de destino: bloquear MANTENIMIENTO siempre
            if (isset($validated['hotel_room_id']) && $validated['hotel_room_id'] != $rent->hotel_room_id) {
                $targetRoom = HotelRoom::find($newRoomId);
                if ($targetRoom && $targetRoom->status === 'MANTENIMIENTO') {
                    DB::connection('tenant')->rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'La habitación seleccionada está en mantenimiento.',
                    ], 422);
                }
            }

            // Detectar conflictos si cambian habitación, fechas u horas
            $datesOrRoomChanged = ($newRoomId    != $rent->hotel_room_id)
                || ($newInputDate  != $rent->input_date)
                || ($newOutputDate != $rent->output_date)
                || ($newInputTime  != ($rent->input_time  ?: '14:00'))
                || ($newOutputTime != ($rent->output_time ?: '12:00'));

            if ($datesOrRoomChanged) {
                $newStart = Carbon::parse("{$newInputDate} {$newInputTime}");
                $newEnd   = Carbon::parse("{$newOutputDate} {$newOutputTime}");
                $conflict = HotelRent::findOverlappingRent($newRoomId, $newStart, $newEnd, $id);
                if ($conflict) {
                    DB::connection('tenant')->rollBack();
                    $cStart = Carbon::parse($conflict->input_date . ' ' . ($conflict->input_time ?: '14:00'))->format('d/m/Y H:i');
                    $cEnd   = Carbon::parse($conflict->output_date . ' ' . ($conflict->output_time ?: '12:00'))->format('d/m/Y H:i');
                    return response()->json([
                        'success' => false,
                        'message' => "La habitación ya está reservada del {$cStart} al {$cEnd}.",
                    ], 422);
                }
            }

            // Construir payload de actualización solo con los campos enviados
            $updatable = array_intersect_key($validated, array_flip([
                'customer_id', 'customer', 'notes', 'license_plate', 'travel_reason',
                'adults', 'children', 'towels', 'hotel_room_id', 'hotel_rate_id',
                'duration', 'quantity_persons', 'input_date', 'input_time',
                'output_date', 'output_time', 'status',
            ]));

            // Si se actualiza customer pero falta id, intentar conservar el original
            if (isset($updatable['customer']) && empty($updatable['customer']['id'])) {
                $currentCustomer = $rent->customer;
                if (is_object($currentCustomer) && isset($currentCustomer->id)) {
                    $updatable['customer']['id'] = $currentCustomer->id;
                }
            }

            // Si cambia la tarifa, refrescar rental_price con el precio configurado
            if (isset($validated['hotel_rate_id']) && $validated['hotel_rate_id'] != $rent->hotel_rate_id) {
                $rate = \Modules\Hotel\Models\HotelRoomRate::where('hotel_room_id', $newRoomId)
                    ->where('hotel_rate_id', $validated['hotel_rate_id'])
                    ->first();
                if ($rate) {
                    $updatable['rental_price'] = (float) $rate->price;
                }
            }

            // Recalcular rental_date_time si cambia input_date/time
            if (isset($validated['input_date']) || isset($validated['input_time'])) {
                $updatable['rental_date_time'] = Carbon::parse(
                    ($validated['input_date'] ?? $rent->input_date) . ' ' .
                    ($validated['input_time'] ?? ($rent->input_time ?: '14:00'))
                );
            }

            // Si cambia la habitación, actualizar también estados
            if (isset($validated['hotel_room_id']) && $validated['hotel_room_id'] != $rent->hotel_room_id) {
                $oldRoom = HotelRoom::find($rent->hotel_room_id);
                $newRoom = HotelRoom::find($validated['hotel_room_id']);
                if ($oldRoom && !$rent->is_reserve) {
                    $oldRoom->status = 'DISPONIBLE';
                    $oldRoom->save();
                }
                if ($newRoom && !$rent->is_reserve) {
                    $newRoom->status = 'OCUPADO';
                    $newRoom->save();
                }
            }

            $rent->update($updatable);

            // Sincronizar el item HAB asociado (si es seguro hacerlo) para que
            // el checkout/factura refleje los nuevos datos de la reserva.
            $this->syncReservationHabItem($rent);

            DB::connection('tenant')->commit();

            $rent->load(['room.category', 'rate', 'items']);

            return response()->json([
                'success' => true,
                'message' => 'Reserva actualizada correctamente.',
                'data'    => $rent,
            ]);
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la reserva: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Reescribe el item HAB asociado a la reserva para reflejar los nuevos
     * datos del alquiler (habitación, tarifa, fechas, duración).
     *
     * Reglas de seguridad:
     *  - Solo aplica si hay EXACTAMENTE un item HAB no facturado/no pagado.
     *  - Si ya hay múltiples items HAB (p.ej. tras un cambio de habitación)
     *    o si está pagado/facturado, no se toca (para no romper historial).
     */
    private function syncReservationHabItem(HotelRent $rent)
    {
        $habItems = $rent->items()
            ->where('type', 'HAB')
            ->whereNull('sale_note_id')
            ->whereNull('document_id')
            ->where('payment_status', '!=', 'PAID')
            ->get();

        if ($habItems->count() !== 1) {
            return;
        }
        $item = $habItems->first();

        $room = HotelRoom::find($rent->hotel_room_id);
        if (!$room) return;

        $itemRecord = \App\Models\Tenant\Item::find($room->item_id);

        $unitPrice = (float) $rent->rental_price;
        if ($unitPrice <= 0) {
            // fallback: tarifa de room_rates
            $rate = \Modules\Hotel\Models\HotelRoomRate::where('hotel_room_id', $room->id)
                ->where('hotel_rate_id', $rent->hotel_rate_id)
                ->first();
            $unitPrice = $rate ? (float) $rate->price : 0;
        }

        $quantity = max(1, (int) $rent->duration);
        $total    = round($unitPrice * $quantity, 4);

        $period    = $rent->rental_period_type ?: 'day';
        $unitLabel = $period === 'hour' ? 'hora(s)' : ($period === 'month' ? 'mes(es)' : 'noche(s)');
        $inputAt   = Carbon::parse($rent->input_date . ' ' . ($rent->input_time ?: '14:00'));
        $outputAt  = Carbon::parse($rent->output_date . ' ' . ($rent->output_time ?: '12:00'));
        $description = sprintf(
            'Estadía en %s - %d %s (%s → %s)',
            $room->name,
            $quantity,
            $unitLabel,
            $inputAt->format('d/m/Y H:i'),
            $outputAt->format('d/m/Y H:i')
        );

        // Reescribir el JSON del item preservando claves auxiliares (igv, charges, etc.)
        $base = is_object($item->item) ? (array) $item->item : ($item->item ?: []);

        $itemId     = $itemRecord ? (int) $itemRecord->id : (int) $room->item_id;
        $internalId = $itemRecord ? $itemRecord->internal_id : ($base['internal_id'] ?? null);
        $name       = $itemRecord ? $itemRecord->name : ($base['name'] ?? $room->name);

        $inner = $base['item'] ?? [];
        if (is_object($inner)) $inner = (array) $inner;
        if (!is_array($inner)) $inner = [];
        $inner = array_merge($inner, [
            'id'               => $itemId,
            'item_id'          => $itemId,
            'internal_id'      => $internalId,
            'name'             => $name,
            'description'      => $description,
            'full_description' => $description,
            'unit_price'       => $unitPrice,
        ]);

        $merged = array_merge($base, [
            'id'                     => $itemId,
            'item_id'                => $itemId,
            'internal_id'            => $internalId,
            'name'                   => $name,
            'description'            => $description,
            'full_description'       => $description,
            'name_product_pdf'       => $description,
            'quantity'               => $quantity,
            'unit_value'             => $unitPrice,
            'unit_price'             => $unitPrice,
            'unit_price_value'       => $unitPrice,
            'input_unit_price_value' => $unitPrice,
            'total'                  => $total,
            'item'                   => $inner,
        ]);

        $item->item_id     = $room->item_id;
        $item->item        = $merged;
        $item->quantity    = $quantity;
        $item->unit_price  = $unitPrice;
        $item->total       = $total;
        $item->description = $description;
        $item->save();
    }

    /**
     * Eliminar/cancelar reserva
     */
    public function deleteReservation($id)
    {
        try {
            $rent = HotelRent::findOrFail($id);

            if ($rent->status === 'FINALIZADO') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una reserva finalizada.',
                ], 422);
            }

            $rent->status = 'FINALIZADO';
            $rent->save();

            return response()->json([
                'success' => true,
                'message' => 'Reserva cancelada correctamente.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Registrar pago de reserva
     */
    private function saveReservationPayment($rentPayment, HotelRentItem $item)
    {
        if ($item->payment_status === 'PAID') {
            $item->payments()->create([
                'date_of_payment' => date('Y-m-d'),
                'payment_method_type_id' => $rentPayment['payment_method_type_id'],
                'reference' => $rentPayment['reference'] ?? null,
                'payment' => $rentPayment['payment'],
            ]);
        }
    }

    /**
     * Obtener total de ventas diarias desde pagos
     */
    public function getDailySalesTotal(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $date = $request->get('date');

        // Obtener pagos de hotel_rent_items para la fecha específica
        $total = HotelRentItem::join('hotel_rent_item_payments', 'hotel_rent_items.id', '=', 'hotel_rent_item_payments.hotel_rent_item_id')
            ->join('hotel_rents', 'hotel_rent_items.hotel_rent_id', '=', 'hotel_rents.id')
            ->whereDate('hotel_rent_item_payments.date_of_payment', $date)
            ->sum('hotel_rent_item_payments.payment');

        return response()->json([
            'total' => $total ?? 0
        ]);
    }

    /**
     * Obtener total de ventas diarias por categoría desde pagos
     */
    public function getCategoryDailySalesTotal(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'category_id' => 'required|integer'
        ]);

        $date = $request->get('date');
        $categoryId = $request->get('category_id');

        // Obtener pagos de hotel_rent_items para la fecha y categoría específica
        $total = HotelRentItem::join('hotel_rent_item_payments', 'hotel_rent_items.id', '=', 'hotel_rent_item_payments.hotel_rent_item_id')
            ->join('hotel_rents', 'hotel_rent_items.hotel_rent_id', '=', 'hotel_rents.id')
            ->join('hotel_rooms', 'hotel_rents.hotel_room_id', '=', 'hotel_rooms.id')
            ->whereDate('hotel_rent_item_payments.date_of_payment', $date)
            ->where('hotel_rooms.hotel_category_id', $categoryId)
            ->sum('hotel_rent_item_payments.payment');

        return response()->json([
            'total' => $total ?? 0
        ]);
    }

    /**
     * Obtener destinos de pago
     */
    private function getPaymentDestinations()
    {
        if (class_exists(\Modules\Finance\Models\GlobalPayment::class)) {
            return \App\Models\Tenant\BankAccount::all()->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'description' => $row->description,
                ];
            });
        }
        return collect();
    }
}
