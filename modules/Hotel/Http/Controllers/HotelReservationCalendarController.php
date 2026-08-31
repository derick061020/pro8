<?php

namespace Modules\Hotel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Hotel\Models\HotelRent;
use Modules\Hotel\Models\HotelRentItem;
use Modules\Hotel\Models\HotelRentItemPayment;
use Modules\Hotel\Models\HotelRentOrder;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\HotelRoomMaintenance;
use Modules\Hotel\Models\HotelCategory;
use Modules\Hotel\Http\Requests\HotelReservationRequest;
use Modules\Hotel\Exports\HotelReservationExport;
use App\Models\Tenant\Person;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\Series;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Company;
use App\Models\Tenant\Document;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\SaleNote;
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

    /**
     * Sucursal (establecimiento) actual del usuario.
     *
     * El calendario debe mostrar SOLO la información de la sucursal en la que
     * está trabajando el usuario. Para todos los usuarios ese valor es
     * `establishment_id` en su registro: los administradores lo cambian con
     * "cambiar establecimiento" (HotelReceptionController@changeUserEstablishment,
     * que persiste el cambio en el usuario), por lo que basta con leerlo aquí.
     */
    private function currentEstablishmentId()
    {
        return auth()->user()->establishment_id;
    }

    public function getCalendarEvents(Request $request)
    {
        $roomId    = $request->get('room_id');
        $status    = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        $establishmentId = $this->currentEstablishmentId();

        // Sincronizar el estado físico de las habitaciones con sus periodos de
        // mantenimiento antes de construir los eventos (auto-actualización).
        HotelRoomMaintenance::reconcile($establishmentId);

        // Solo reservas de habitaciones de la sucursal actual. Se filtra por la
        // habitación (fuente de verdad de a qué sucursal pertenece) para no
        // mezclar información entre sucursales.
        $query = HotelRent::with(['room', 'room.category', 'items'])
            ->whereHas('room', function ($q) use ($establishmentId) {
                $q->where('establishment_id', $establishmentId);
            });

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

        // Pagos aplicados por reserva (una sola consulta agregada). Se usa para
        // colorear las barras según el estado de pago: pagado / adelanto / debe.
        // Misma base que calculateRentDebt: deuda = Σ(items no-PAY) − pagos + arrears.
        $paidByRent = HotelRentItemPayment::query()
            ->join('hotel_rent_items', 'hotel_rent_items.id', '=', 'hotel_rent_item_payments.hotel_rent_item_id')
            ->whereIn('hotel_rent_items.hotel_rent_id', $reservations->pluck('id'))
            ->groupBy('hotel_rent_items.hotel_rent_id')
            ->selectRaw('hotel_rent_items.hotel_rent_id as rent_id, SUM(hotel_rent_item_payments.payment) as paid')
            ->pluck('paid', 'rent_id');

        $events = $reservations->map(function ($reservation) use ($paidByRent) {
            $inputAt  = Carbon::parse($reservation->input_date);
            $outputAt = Carbon::parse($reservation->output_date);

            $customerData      = $reservation->customer;
            $customerName      = (is_object($customerData) && isset($customerData->name)) ? $customerData->name : 'N/A';
            $customerAddress   = (is_object($customerData) && isset($customerData->address)) ? $customerData->address : null;
            $customerTelephone = (is_object($customerData) && isset($customerData->telephone)) ? $customerData->telephone : null;
            $customerNumber    = (is_object($customerData) && isset($customerData->number)) ? $customerData->number : null;

            $roomName     = $reservation->room ? $reservation->room->name : 'Habitación eliminada';
            $roomCategory = ($reservation->room && $reservation->room->category) ? $reservation->room->category->description : 'N/A';

            $pay = $this->computeReservationPayment($reservation, (float) ($paidByRent[$reservation->id] ?? 0));

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
                'reservation_origin' => $reservation->reservation_origin,
                'notes'              => $reservation->notes,
                'is_reserve'         => (bool) $reservation->is_reserve,
                // Estado de pago para el color de la barra: paid | partial | unpaid.
                'paid'               => $pay['paid'],
                'debt'               => $pay['debt'],
                'payment_state'      => $pay['state'],
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
        // Importe = cargos + mora. Es exactamente la base de la deuda, así que
        // siempre se cumple  importe = pagado + deuda.
        return round(
            $this->chargeableItemsTotal($reservation) + (float) ($reservation->arrears ?? 0),
            2
        );
    }

    /**
     * Suma de los items que son cargos reales de la reserva.
     *
     * Los items de tipo PAY NO son cargos: son adelantos/créditos, y su
     * `item.total` guarda el monto entregado por el huésped (ver el "Adelanto
     * de pago" que crea HotelRentController). Sumarlos inflaba el importe con
     * el propio pago y rompía la identidad importe = pagado + deuda.
     *
     * Es la única suma de cargos del controlador: el importe y la deuda salen
     * de aquí para que no puedan divergir.
     */
    private function chargeableItemsTotal(HotelRent $reservation)
    {
        if (!$reservation->relationLoaded('items')) {
            $reservation->load('items');
        }

        return (float) $reservation->items
            ->filter(function ($item) { return $item->type !== 'PAY'; })
            ->sum(function ($item) {
                $col = (float) $item->total;
                if ($col > 0) return $col;
                $json = is_object($item->item) ? (array) $item->item : ($item->item ?: []);
                return (float) ($json['total'] ?? 0);
            });
    }

    /**
     * Estado de pago de una reserva para colorear la barra del calendario.
     * Reutiliza la misma fórmula de deuda que el resto del sistema:
     *   deuda = Σ(items no-PAY) − pagos aplicados + arrears.
     *
     * @param  float|null $paid  Suma de pagos ya calculada (evita N+1 en el
     *                           listado); si es null se consulta aquí.
     * @return array{paid: float, debt: float, state: string}
     *         state: 'paid' (pagado) | 'partial' (adelanto) | 'unpaid' (sin pagar).
     */
    private function computeReservationPayment(HotelRent $reservation, $paid = null)
    {
        if (!$reservation->relationLoaded('items')) {
            $reservation->load('items');
        }

        if ($paid === null) {
            $paid = (float) HotelRentItemPayment::query()
                ->join('hotel_rent_items', 'hotel_rent_items.id', '=', 'hotel_rent_item_payments.hotel_rent_item_id')
                ->where('hotel_rent_items.hotel_rent_id', $reservation->id)
                ->sum('hotel_rent_item_payments.payment');
        }
        $paid = (float) $paid;

        // Misma suma de cargos que el importe (excluye los PAY, que son
        // adelantos y no cargos).
        $totalItems = $this->chargeableItemsTotal($reservation);

        $debt = round($totalItems - $paid + (float) ($reservation->arrears ?? 0), 2);

        if ($debt <= 0.009) {
            $state = 'paid';           // Saldado (o con vuelto a favor).
        } elseif ($paid > 0) {
            $state = 'partial';        // Adelanto / pago parcial pendiente.
        } else {
            $state = 'unpaid';         // No se ha pagado nada.
        }

        return ['paid' => round($paid, 2), 'debt' => $debt, 'state' => $state];
    }

    /**
     * Habitaciones de la sucursal actual para el calendario.
     *
     * Se devuelven TODAS las habitaciones activas de la sucursal (sin paginar)
     * con su categoría y tarifas, para que la grilla las agrupe correctamente y
     * no mezcle habitaciones de otras sucursales. Antes el calendario consumía
     * /hotels/rooms, que para un usuario admin no filtraba por sucursal y además
     * paginaba a 25 filas, provocando la mezcla de información entre sucursales.
     */
    public function getRooms()
    {
        $establishmentId = $this->currentEstablishmentId();

        // Auto-actualizar estados según los periodos de mantenimiento vigentes.
        HotelRoomMaintenance::reconcile($establishmentId);

        $rooms = HotelRoom::with(['category', 'rates.rate'])
            ->where('establishment_id', $establishmentId)
            ->where('active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($room) {
                $data = $room->toArray();
                // Normalizar tarifas para el formulario de edición inline.
                $data['rates'] = $room->rates->map(function ($rr) {
                    return [
                        'hotel_rate_id'    => $rr->hotel_rate_id,
                        'price'            => (float) $rr->price,
                        'rate_description' => $rr->rate ? $rr->rate->description : null,
                    ];
                })->values();
                return $data;
            });

        return response()->json([
            'data' => $rooms
        ]);
    }

    /**
     * Categorías (tipos de habitación) de la sucursal actual para el filtro del
     * calendario. Se filtra por sucursal para no ofrecer tipos de otras sedes.
     */
    public function getCategories()
    {
        $categories = HotelCategory::where('active', true)
            ->where('establishment_id', $this->currentEstablishmentId())
            ->orderBy('description')
            ->get()
            ->map(function ($c) {
                return [
                    'id'          => $c->id,
                    'description' => $c->description,
                ];
            });

        return response()->json([
            'data' => $categories
        ]);
    }

    /**
     * Periodos de mantenimiento de la sucursal actual que solapan con el rango
     * de fechas visible del calendario. Se usan para pintar las barras grises de
     * "mantenimiento" junto a las reservas.
     */
    public function getMaintenances(Request $request)
    {
        $establishmentId = $this->currentEstablishmentId();
        HotelRoomMaintenance::reconcile($establishmentId);

        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        $query = HotelRoomMaintenance::where('establishment_id', $establishmentId);

        if ($startDate && $endDate) {
            // Solapamiento estándar: start <= rango.fin AND end >= rango.inicio
            $query->whereDate('start_date', '<=', $endDate)
                  ->whereDate('end_date', '>=', $startDate);
        }

        $items = $query->orderBy('start_date', 'asc')->get()->map(function ($m) {
            return [
                'id'            => $m->id,
                'hotel_room_id' => $m->hotel_room_id,
                'start_date'    => Carbon::parse($m->start_date)->format('Y-m-d'),
                'end_date'      => Carbon::parse($m->end_date)->format('Y-m-d'),
                'reason'        => $m->reason,
                'status'        => $m->status,
            ];
        });

        return response()->json(['data' => $items]);
    }

    /**
     * Programa un periodo de mantenimiento para una habitación. Las fechas son
     * inclusivas: la habitación quedará en mantenimiento desde start_date hasta
     * end_date y volverá a estar disponible el día siguiente.
     */
    public function storeMaintenance(Request $request)
    {
        $request->validate([
            'hotel_room_id' => 'required|integer',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'reason'        => 'nullable|string|max:250',
        ]);

        $establishmentId = $this->currentEstablishmentId();

        $room = HotelRoom::where('id', $request->get('hotel_room_id'))
            ->where('establishment_id', $establishmentId)
            ->first();

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'La habitación no pertenece a esta sucursal.',
            ], 422);
        }

        // Evitar solapar el mantenimiento con reservas ya existentes en esas fechas.
        $overlappingReservation = HotelRent::where('hotel_room_id', $room->id)
            ->where('status', '!=', 'FINALIZADO')
            ->whereDate('input_date', '<=', $request->get('end_date'))
            ->whereDate('output_date', '>=', $request->get('start_date'))
            ->exists();

        if ($overlappingReservation) {
            return response()->json([
                'success' => false,
                'message' => 'La habitación ya tiene una reserva en esas fechas.',
            ], 422);
        }

        $maintenance = HotelRoomMaintenance::create([
            'hotel_room_id'    => $room->id,
            'establishment_id' => $establishmentId,
            'start_date'       => $request->get('start_date'),
            'end_date'         => $request->get('end_date'),
            'reason'           => $request->get('reason'),
            'status'           => 'SCHEDULED',
        ]);

        // Aplicar de inmediato si el periodo ya está en curso hoy.
        HotelRoomMaintenance::reconcile($establishmentId);

        return response()->json([
            'success' => true,
            'message' => 'Mantenimiento programado correctamente.',
            'data'    => $maintenance,
        ]);
    }

    /**
     * Elimina/cancela un periodo de mantenimiento. Si estaba en curso, devuelve
     * la habitación a disponible (salvo que otro periodo la mantenga).
     */
    public function deleteMaintenance($id)
    {
        $establishmentId = $this->currentEstablishmentId();

        $maintenance = HotelRoomMaintenance::where('id', $id)
            ->where('establishment_id', $establishmentId)
            ->first();

        if (!$maintenance) {
            return response()->json([
                'success' => false,
                'message' => 'Mantenimiento no encontrado.',
            ], 404);
        }

        $room = HotelRoom::find($maintenance->hotel_room_id);
        $maintenance->delete();

        // Si la habitación estaba en mantenimiento por este periodo y no hay otro
        // vigente, devolverla a disponible.
        if ($room && $room->status === 'MANTENIMIENTO') {
            $stillInMaintenance = HotelRoomMaintenance::where('hotel_room_id', $room->id)
                ->where('status', '!=', 'DONE')
                ->whereDate('start_date', '<=', Carbon::today()->toDateString())
                ->whereDate('end_date', '>=', Carbon::today()->toDateString())
                ->exists();
            if (!$stillInMaintenance) {
                $room->status = 'DISPONIBLE';
                $room->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Mantenimiento eliminado.',
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

        // El cliente se guarda como JSON en la columna `customer`. Para reservas
        // creadas desde la web pública ese JSON no siempre trae el `id` ni la
        // `description` que el formulario de recepción necesita para mostrar el
        // cliente en su selector. Cuando la reserva sí tiene un customer_id real,
        // se completa con la ficha de Person para que la edición muestre y conserve
        // el cliente correctamente.
        $customerData = $reservation->customer;
        $person = null;
        $personId = $reservation->customer_id
            ?: (is_object($customerData) ? ($customerData->id ?? null) : null);
        if ($personId) {
            $person = Person::withOut('department', 'province', 'district')->find($personId);
        }

        $jsonName   = is_object($customerData) ? ($customerData->name ?? null) : null;
        $jsonNumber = is_object($customerData) ? ($customerData->number ?? null) : null;
        $name       = $person->name ?? $jsonName ?? 'N/A';
        $number     = $person->number ?? $jsonNumber ?? null;
        // `description` es lo que muestra el <el-option> del selector de clientes
        // en recepción (Rent.vue). Se arma con número + nombre como hace el sistema.
        $description = $person->description
            ?? (is_object($customerData) ? ($customerData->description ?? null) : null)
            ?? trim(($number ? $number . ' - ' : '') . ($name ?: ''), ' -')
            ?: $name;

        $customerDetails = [
            'id'                        => $person->id ?? $personId ?? null,
            'name'                      => $name,
            'description'               => $description,
            'address'                   => $person->address ?? (is_object($customerData) ? ($customerData->address ?? null) : null),
            'telephone'                 => $person->telephone ?? (is_object($customerData) ? ($customerData->telephone ?? null) : null),
            'email'                     => $person->email ?? (is_object($customerData) ? ($customerData->email ?? null) : null),
            'number'                    => $number,
            'identity_document_type_id' => $person->identity_document_type_id
                ?? (is_object($customerData) ? ($customerData->identity_document_type_id ?? null) : null),
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

        // Precio unitario efectivo de la estadía. Para reservas creadas desde la
        // web el precio real vive en el item HAB (puede ser el web_price de la
        // habitación) mientras que `rental_price` queda en 0. Se prioriza
        // `rental_price` (edición manual) y, si no hay, el unitario del item HAB.
        $habItem = $reservation->items->firstWhere('type', 'HAB') ?: $reservation->items->first();
        $habUnitPrice = 0.0;
        if ($habItem) {
            $habJson = is_object($habItem->item) ? (array) $habItem->item : ($habItem->item ?: []);
            $habUnitPrice = (float) $habItem->unit_price > 0
                ? (float) $habItem->unit_price
                : (float) ($habJson['unit_price'] ?? $habJson['unit_price_value'] ?? 0);
        }
        $effectiveUnitPrice = (float) $reservation->rental_price > 0
            ? (float) $reservation->rental_price
            : $habUnitPrice;

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

        $payment = $this->computeReservationPayment($reservation);

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
                // Precio unitario efectivo (incluye el precio propio de la web).
                'rental_price'     => $effectiveUnitPrice,
                'unit_price'       => $effectiveUnitPrice,
            ],
            'totals'              => [
                'total'         => $this->computeReservationTotal($reservation),
                'paid'          => $payment['paid'],
                'debt'          => $payment['debt'],
                'payment_state' => $payment['state'],
            ],
            'status'              => $reservation->status,
            'adults'              => $reservation->adults,
            'children'            => $reservation->children,
            'quantity_persons'    => $reservation->quantity_persons,
            'towels'              => $reservation->towels,
            'license_plate'       => $reservation->license_plate,
            'travel_reason'       => $reservation->travel_reason,
            'reservation_origin'  => $reservation->reservation_origin,
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
     * Exportar a Excel el reporte de reservas con filtros.
     *
     * Pensado para el uso diario de recepción: por defecto exporta LAS RESERVAS
     * DE UN DÍA (start = end), y admite también un rango. El criterio de fecha
     * es configurable porque "las reservas del día" significa cosas distintas
     * según la tarea:
     *
     *   - input   → las que ingresan ese día (llegadas del día). Por defecto.
     *   - output  → las que salen ese día (salidas del día).
     *   - stay    → las que están alojadas ese día (ocupación).
     *   - created → las registradas ese día (producción de reservas).
     *
     * Filtros adicionales: estado, categoría, habitación, medio de reserva y
     * estado de pago.
     */
    /**
     * Página del reporte de reservas: filtros arriba y la tabla en pantalla.
     * Las descargas (PDF / Excel) salen de la misma página con los mismos
     * filtros aplicados.
     */
    public function report()
    {
        $establishment = Establishment::find($this->currentEstablishmentId());

        return view('hotel::reservations.report', compact('establishment'));
    }

    /**
     * Datos del reporte para la página.
     */
    public function reportData(Request $request)
    {
        $report = $this->buildReservationReport($request);

        return response()->json([
            'success'     => true,
            'records'     => $report['records'],
            'totals'      => $report['totals'],
            'by_day'      => $report['by_day'],
            'by_category' => $report['by_category'],
            'criterion'   => $report['criterion'],
            'by_day_title'=> $report['by_day_title'],
            'by_day_hint' => $report['by_day_hint'],
            'period'      => $report['period'],
            'filters'     => $report['filters'],
        ], 200);
    }

    /**
     * Descarga del reporte: PDF con `format=pdf`, Excel en cualquier otro caso.
     */
    public function exportReservations(Request $request)
    {
        $report = $this->buildReservationReport($request);

        if ($request->get('format') === 'pdf') {
            return $this->reservationsPdf($report);
        }

        return (new HotelReservationExport)
            ->records($report['records'])
            ->company($report['company'])
            ->establishment($report['establishment'])
            ->filters($report['filters'])
            ->totals($report['totals'])
            ->download($report['basename'] . '.xlsx');
    }

    /**
     * Consulta y agrega el reporte de reservas.
     *
     * Es la única fuente de datos: la página, el Excel y el PDF salen de aquí,
     * así que lo que se ve en pantalla es exactamente lo que se descarga.
     */
    private function buildReservationReport(Request $request)
    {
        $establishmentId = $this->currentEstablishmentId();
        $establishment   = Establishment::find($establishmentId);

        $start = $this->parseReportDate($request->get('start')) ?: Carbon::now()->startOfDay();
        $end   = $this->parseReportDate($request->get('end')) ?: $start->copy();

        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        $startStr = $start->format('Y-m-d');
        $endStr   = $end->format('Y-m-d');

        $dateField = in_array($request->get('date_field'), ['input', 'output', 'stay', 'created'], true)
            ? $request->get('date_field')
            : 'input';

        $query = HotelRent::with(['room', 'room.category', 'items'])
            ->whereHas('room', function ($q) use ($establishmentId) {
                $q->where('establishment_id', $establishmentId);
            });

        switch ($dateField) {
            case 'output':
                $query->whereBetween('output_date', [$startStr, $endStr]);
                break;
            case 'stay':
                // Estadías que se solapan con el rango.
                $query->where('input_date', '<=', $endStr)
                      ->where('output_date', '>=', $startStr);
                break;
            case 'created':
                $query->whereBetween('created_at', [$startStr . ' 00:00:00', $endStr . ' 23:59:59']);
                break;
            case 'input':
            default:
                $query->whereBetween('input_date', [$startStr, $endStr]);
                break;
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('room_id')) {
            $query->where('hotel_room_id', $request->get('room_id'));
        }

        if ($request->filled('category_id')) {
            $categoryId = $request->get('category_id');
            $query->whereHas('room', function ($q) use ($categoryId) {
                $q->where('hotel_category_id', $categoryId);
            });
        }

        if ($request->filled('origin')) {
            $query->where('reservation_origin', $request->get('origin'));
        }

        if ($request->filled('reservation_type')) {
            // web/manual: `is_reserve` marca las reservas (frente a check-in directo).
            $query->where('is_reserve', $request->get('reservation_type') === 'reserve');
        }

        $reservations = $query
            ->orderBy('input_date')
            ->orderBy('input_time')
            ->get();

        // Pagos agregados en una sola consulta (evita N+1).
        $paidByRent = HotelRentItemPayment::query()
            ->join('hotel_rent_items', 'hotel_rent_items.id', '=', 'hotel_rent_item_payments.hotel_rent_item_id')
            ->whereIn('hotel_rent_items.hotel_rent_id', $reservations->pluck('id'))
            ->groupBy('hotel_rent_items.hotel_rent_id')
            ->selectRaw('hotel_rent_items.hotel_rent_id as rent_id, SUM(hotel_rent_item_payments.payment) as paid')
            ->pluck('paid', 'rent_id');

        $rentIds       = $reservations->pluck('id')->all();
        $documentsMap  = $rentIds
            ? Document::whereIn('hotel_rent_id', $rentIds)->get()->keyBy('hotel_rent_id')
            : collect();
        $saleNotesMap  = $rentIds
            ? SaleNote::whereIn('hotel_rent_id', $rentIds)->get()->keyBy('hotel_rent_id')
            : collect();

        $paymentStateLabels = [
            'paid'    => 'Pagado',
            'partial' => 'Adelanto / parcial',
            'unpaid'  => 'Sin pagar',
        ];

        $paymentFilter = $request->get('payment_state');

        $records = $reservations->map(function (HotelRent $reservation) use ($paidByRent, $documentsMap, $saleNotesMap, $paymentStateLabels) {
            $pay = $this->computeReservationPayment($reservation, (float) ($paidByRent[$reservation->id] ?? 0));

            $customerData = $reservation->customer;
            $customerName = (is_object($customerData) && isset($customerData->name)) ? $customerData->name : '';

            $document = $documentsMap->get($reservation->id) ?: $saleNotesMap->get($reservation->id);

            return [
                'id'                 => $reservation->id,
                'status'             => $reservation->is_reserve ? 'Reserva' : ($reservation->status === 'FINALIZADO' ? 'Finalizado' : 'En curso'),
                'room'               => $reservation->room->name ?? '',
                'category'           => $reservation->room->category->description ?? '',
                'customer'           => $customerName,
                'customer_number'    => (is_object($customerData) && isset($customerData->number)) ? $customerData->number : '',
                'customer_telephone' => (is_object($customerData) && isset($customerData->telephone)) ? $customerData->telephone : '',
                'adults'             => $reservation->adults,
                'children'           => $reservation->children,
                'input_date'         => $reservation->input_date,
                'input_time'         => $reservation->input_time,
                'output_date'        => $reservation->output_date,
                'output_time'        => $reservation->output_time,
                'duration'           => $reservation->duration,
                'origin'             => $this->reservationOriginLabel($reservation->reservation_origin),
                'rental_price'       => (float) $reservation->rental_price,
                'total'              => $this->computeReservationTotal($reservation),
                'paid'               => $pay['paid'],
                'debt'               => $pay['debt'],
                'payment_key'        => $pay['state'],
                'payment_state'      => $paymentStateLabels[$pay['state']] ?? $pay['state'],
                'document_number'    => $document ? ($document->series . '-' . $document->number) : '',
                'license_plate'      => $reservation->license_plate,
                'travel_reason'      => $reservation->travel_reason,
                'notes'              => $reservation->notes,
                'created_at'         => optional($reservation->created_at)->format('d/m/Y H:i'),
                // Fecha de registro en crudo: la agrupación por día la necesita
                // cuando el criterio elegido es "fecha de registro".
                'created_date'       => optional($reservation->created_at)->format('Y-m-d'),
            ];
        });

        if (in_array($paymentFilter, ['paid', 'partial', 'unpaid'], true)) {
            $records = $records->where('payment_key', $paymentFilter);
        }

        $records = $records->values();

        $totals = [
            'total'  => round($records->sum('total'), 2),
            'paid'   => round($records->sum('paid'), 2),
            'debt'   => round($records->sum('debt'), 2),
            'nights' => (int) $records->sum('duration'),
            'count'  => $records->count(),
            // Cuántas personas entran en el periodo: es el dato que se mira en
            // la hoja impresa (recepción quiere saber cuántos ingresarán) antes
            // de abrir el sistema.
            'adults'   => (int) $records->sum('adults'),
            'children' => (int) $records->sum('children'),
            'guests'   => (int) $records->sum('adults') + (int) $records->sum('children'),
            'rooms'    => $records->pluck('room')->filter()->unique()->count(),
        ];

        $dateFieldLabels = [
            'input'   => 'Fecha de ingreso',
            'output'  => 'Fecha de salida',
            'stay'    => 'Alojados en la fecha',
            'created' => 'Fecha de registro',
        ];

        $filters = [
            'Periodo'        => $startStr === $endStr
                ? $start->format('d/m/Y')
                : $start->format('d/m/Y') . ' al ' . $end->format('d/m/Y'),
            'Criterio'       => $dateFieldLabels[$dateField],
            'Reservas'       => $totals['count'],
        ];

        if ($request->filled('status')) {
            $filters['Estado'] = $request->get('status') === 'FINALIZADO' ? 'Finalizado' : 'En curso';
        }
        if ($request->filled('category_id')) {
            $filters['Tipo de habitación'] = optional(HotelCategory::find($request->get('category_id')))->description;
        }
        if ($request->filled('room_id')) {
            $filters['Habitación'] = optional(HotelRoom::find($request->get('room_id')))->name;
        }
        if ($request->filled('origin')) {
            $filters['Medio de reserva'] = $this->reservationOriginLabel($request->get('origin'));
        }
        if (in_array($paymentFilter, ['paid', 'partial', 'unpaid'], true)) {
            $filters['Estado de pago'] = $paymentStateLabels[$paymentFilter];
        }

        // Resumen por día. Se agrupa por la MISMA fecha con la que se filtró:
        // si el criterio es la salida, agrupar por la entrada mostraba días
        // fuera del periodo elegido.
        $groupField = [
            'input'   => 'input_date',
            'output'  => 'output_date',
            'created' => 'created_date',
            'stay'    => 'input_date',   // una estadía abarca varios días: se corta por la entrada
        ][$dateField];

        $byDayLabels = [
            'input'   => ['Ingresos por día', 'cuánta gente entra cada fecha'],
            'output'  => ['Salidas por día', 'cuánta gente sale cada fecha'],
            'created' => ['Reservas registradas por día', 'cuándo se registró cada reserva'],
            'stay'    => ['Ingresos por día', 'por fecha de entrada de cada estadía'],
        ];

        $byDay = $records
            ->groupBy(function ($row) use ($groupField) {
                return substr((string) $row[$groupField], 0, 10);
            })
            ->map(function ($rows, $date) {
                return [
                    'date'     => $date,
                    'count'    => $rows->count(),
                    'adults'   => (int) $rows->sum('adults'),
                    'children' => (int) $rows->sum('children'),
                    'guests'   => (int) $rows->sum('adults') + (int) $rows->sum('children'),
                    'rooms'    => $rows->pluck('room')->filter()->unique()->count(),
                    'nights'   => (int) $rows->sum('duration'),
                    'total'    => round($rows->sum('total'), 2),
                    'debt'     => round($rows->sum('debt'), 2),
                ];
            })
            ->sortBy('date')
            ->values();

        // Resumen por tipo de habitación.
        $byCategory = $records
            ->groupBy(function ($row) {
                return $row['category'] !== '' ? $row['category'] : 'Sin tipo';
            })
            ->map(function ($rows, $category) {
                return [
                    'category' => $category,
                    'count'    => $rows->count(),
                    'guests'   => (int) $rows->sum('adults') + (int) $rows->sum('children'),
                    'nights'   => (int) $rows->sum('duration'),
                    'total'    => round($rows->sum('total'), 2),
                ];
            })
            ->sortByDesc('count')
            ->values();

        return [
            'records'       => $records,
            'totals'        => $totals,
            'filters'       => $filters,
            'by_day'        => $byDay,
            'by_category'   => $byCategory,
            'criterion'     => $dateFieldLabels[$dateField],
            'by_day_title'  => $byDayLabels[$dateField][0],
            'by_day_hint'   => $byDayLabels[$dateField][1],
            'period'        => $filters['Periodo'],
            'start'         => $start,
            'end'           => $end,
            'company'       => Company::first(),
            'establishment' => $establishment,
            'basename'      => 'Reporte_reservas_' . $startStr . ($startStr === $endStr ? '' : '_al_' . $endStr),
        ];
    }

    /**
     * Versión PDF del reporte de reservas (A4 apaisado).
     *
     * Además del detalle, arma dos resúmenes que en Excel había que calcular a
     * mano: cuántas personas y habitaciones entran cada día, y cómo se reparten
     * por tipo de habitación.
     */
    /**
     * Versión PDF del reporte (A4 apaisado), a partir del reporte ya armado.
     */
    private function reservationsPdf(array $report)
    {
        $html = view('hotel::reservations.report_pdf', [
            'records'       => $report['records'],
            'company'       => $report['company'],
            'establishment' => $report['establishment'],
            'filters'       => $report['filters'],
            'totals'        => $report['totals'],
            'criterion'     => $report['criterion'],
            'start'         => $report['start'],
            'end'           => $report['end'],
            'byDay'         => $report['by_day'],
            'byCategory'    => $report['by_category'],
            'byDayTitle'    => $report['by_day_title'],
            'byDayHint'     => $report['by_day_hint'],
        ])->render();

        // pcre.backtrack_limit por defecto se queda corto con tablas largas y
        // mPDF devuelve una página en blanco sin avisar.
        ini_set('pcre.backtrack_limit', '50000000');

        $pdf = new \Mpdf\Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4-L',
            'default_font'  => 'dejavusanscondensed',
            'margin_top'    => 28,
            'margin_bottom' => 16,
            'margin_left'   => 8,
            'margin_right'  => 8,
            'margin_header' => 6,
            'margin_footer' => 6,
        ]);

        $filename = $report['basename'] . '.pdf';

        $pdf->SetTitle('Reporte de reservas');
        $pdf->SetAuthor(optional($report['company'])->name ?: 'Hotel');
        $pdf->WriteHTML($html);

        return response(
            $pdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]
        );
    }

    /**
     * Parsea una fecha del reporte, tolerando formato vacío o inválido.
     */
    private function parseReportDate($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable $th) {
            return null;
        }
    }

    /**
     * Etiqueta legible del medio de reserva.
     */
    private function reservationOriginLabel($origin)
    {
        $labels = [
            'whatsapp'   => 'WhatsApp',
            'correo'     => 'Correo',
            'celular'    => 'Celular',
            'presencial' => 'Presencial',
            'web'        => 'Web',
        ];

        return $origin ? ($labels[$origin] ?? ucfirst($origin)) : '';
    }

    /**
     * Datos necesarios para el formulario de reserva (mismo patrón que Rent)
     *
     * Optimización: la lista inicial de clientes era de 20 personas con todos
     * los campos; en bases grandes esto causaba un retraso notable al abrir
     * el formulario. Cargamos lista vacía: el frontend usa remote search.
     */
    public function getFormTables()
    {
        $payment_method_types = PaymentMethodType::all();
        $payment_destinations = $this->getPaymentDestinations();
        $configuration = Configuration::first();
        $affectation_igv_types = AffectationIgvType::whereActive()->get();
        $series = Series::where('establishment_id', auth()->user()->establishment_id)->get();

        return response()->json([
            'customers' => [], // remote search via searchCustomers
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

            // El adelanto es un pago parcial: la reserva queda como DEBT pero
            // se registra el monto adelantado.
            $isAdvancePayment = $request->payment_status === 'ADVANCE';
            $effectivePaymentStatus = $isAdvancePayment ? 'DEBT' : $request->payment_status;

            $data = $request->only(
                'customer_id', 'customer', 'notes', 'license_plate', 'travel_reason', 'reservation_origin',
                'adults', 'children', 'towels', 'hotel_room_id', 'hotel_rate_id',
                'duration', 'quantity_persons', 'output_date',
                'output_time', 'input_date', 'input_time', 'data_persons'
            );
            $data['payment_status'] = $effectivePaymentStatus;
            $data['is_reserve'] = true;
            $data['status'] = 'ACTIVE';
            $data['establishment_id'] = $room->establishment_id;

            $rent = HotelRent::create($data);

            // Crear orden
            $order = new HotelRentOrder();
            $order->hotel_rent_id = $rent->id;
            $order->order_number = 1;
            $order->order_status = $effectivePaymentStatus;
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
                $item->payment_status = $effectivePaymentStatus;
                $item->hotel_rent_order_id = $order->id;
                $item->save();

                // Registrar pago si aplica (pago completo o adelanto parcial)
                if (($request->payment_status === 'PAID' || $isAdvancePayment)
                    && $request->rent_payment
                    && ($request->rent_payment['payment'] ?? 0) > 0) {
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
            'reservation_origin' => 'nullable|in:whatsapp,correo,celular,presencial',
            'adults'             => 'nullable|integer|min:0',
            'children'           => 'nullable|integer|min:0',
            'towels'             => 'nullable|integer|min:0',
            'hotel_room_id'      => 'nullable|integer',
            'hotel_rate_id'      => 'nullable|integer',
            'rental_price'       => 'nullable|numeric|min:0',
            'duration'           => 'nullable|integer|min:1',
            'quantity_persons'   => 'nullable|integer|min:1',
            'input_date'         => 'nullable|date_format:Y-m-d',
            'input_time'         => 'nullable|date_format:H:i',
            'output_date'        => 'nullable|date_format:Y-m-d',
            'output_time'        => 'nullable|date_format:H:i',
            'status'             => 'nullable|string|max:30',
            // Estado de pago editable desde el formulario de la reserva.
            'payment_status'                      => 'nullable|string|max:20',
            'rent_payment'                        => 'nullable|array',
            'rent_payment.payment'                => 'nullable|numeric|min:0',
            'rent_payment.payment_method_type_id' => 'nullable|string|max:5',
            'rent_payment.payment_destination_id' => 'nullable|string|max:50',
            'rent_payment.reference'              => 'nullable|string|max:255',
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
                'customer_id', 'customer', 'notes', 'license_plate', 'travel_reason', 'reservation_origin',
                'adults', 'children', 'towels', 'hotel_room_id', 'hotel_rate_id',
                'rental_price', 'duration', 'quantity_persons', 'input_date', 'input_time',
                'output_date', 'output_time', 'status',
            ]));

            // Si se actualiza customer pero falta id, intentar conservar el original
            if (isset($updatable['customer']) && empty($updatable['customer']['id'])) {
                $currentCustomer = $rent->customer;
                if (is_object($currentCustomer) && isset($currentCustomer->id)) {
                    $updatable['customer']['id'] = $currentCustomer->id;
                }
            }

            // La tarifa se normaliza contra el catálogo (`hotel_rates`): si el id
            // enviado no existe allí se descarta en vez de romper la FK del
            // alquiler con un error SQL.
            if (isset($updatable['hotel_rate_id'])) {
                $resolvedRateId = \Modules\Hotel\Models\HotelRate::resolveCatalogId(
                    $updatable['hotel_rate_id'],
                    $newRoomId
                );

                if ($resolvedRateId) {
                    $updatable['hotel_rate_id'] = $resolvedRateId;
                    $validated['hotel_rate_id'] = $resolvedRateId;
                } else {
                    unset($updatable['hotel_rate_id'], $validated['hotel_rate_id']);
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

            // Aplicar el estado de pago elegido en el formulario. Va DESPUÉS de
            // sincronizar el item para que el monto a cobrar use el total ya
            // actualizado (tarifa/noches nuevas).
            $this->applyReservationPaymentUpdate(
                $rent,
                $validated['payment_status'] ?? null,
                $validated['rent_payment'] ?? []
            );

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
            // Preservar el precio propio ya guardado en el item (p. ej. el
            // web_price de una reserva creada desde la web) antes de recurrir a
            // la tarifa base, para no perder el precio real de la reserva.
            $existingJson = is_object($item->item) ? (array) $item->item : ($item->item ?: []);
            $existingUnit = (float) $item->unit_price > 0
                ? (float) $item->unit_price
                : (float) ($existingJson['unit_price'] ?? $existingJson['unit_price_value'] ?? 0);
            if ($existingUnit > 0) {
                $unitPrice = $existingUnit;
            } else {
                // fallback: tarifa de room_rates
                $rate = \Modules\Hotel\Models\HotelRoomRate::where('hotel_room_id', $room->id)
                    ->where('hotel_rate_id', $rent->hotel_rate_id)
                    ->first();
                $unitPrice = $rate ? (float) $rate->price : 0;
            }
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
     * Eliminar reserva (HARD DELETE).
     *
     * Marcar `FINALIZADO` no era suficiente: la reserva seguía bloqueando la
     * habitación en validaciones, calendarios y reportes. Para reservas que se
     * cancelan/se equivocan, el operador espera que la fila desaparezca por
     * completo. Se permite borrar aunque la reserva tenga pagos: en ese caso se
     * eliminan también los movimientos de caja (global_payment) asociados a cada
     * pago para que caja/ventas no queden descuadradas con registros huérfanos.
     */
    public function deleteReservation($id)
    {
        DB::connection('tenant')->beginTransaction();
        try {
            $rent = HotelRent::with('items')->findOrFail($id);

            // Si la habitación quedó como OCUPADO solo por esta reserva, liberarla
            $room = HotelRoom::find($rent->hotel_room_id);

            // Eliminar items + pagos (con su movimiento en caja) + órdenes y luego el rent.
            // OJO: HotelRentItem::payments() es hasOne, pero un item puede tener varias
            // filas de pago (adelanto + saldo). Se consultan todas por hotel_rent_item_id
            // para no dejar pagos ni movimientos de caja huérfanos.
            foreach ($rent->items as $item) {
                $itemPayments = HotelRentItemPayment::where('hotel_rent_item_id', $item->id)->get();
                foreach ($itemPayments as $payment) {
                    // Borrar el movimiento en caja para no dejar dinero fantasma
                    if ($payment->global_payment) {
                        $payment->global_payment()->delete();
                    }
                    $payment->delete();
                }
                $item->delete();
            }
            $rent->orders()->delete();
            $rent->delete();

            if ($room && $room->status === 'OCUPADO') {
                $stillActive = HotelRent::where('hotel_room_id', $room->id)
                    ->where('status', '!=', 'FINALIZADO')
                    ->exists();
                if (!$stillActive) {
                    $room->status = 'DISPONIBLE';
                    $room->save();
                }
            }

            DB::connection('tenant')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Reserva eliminada correctamente.',
            ]);
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollBack();
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
        // Registrar el pago tanto si la reserva está pagada (PAID) como si solo
        // se registró un adelanto (item en DEBT con un monto parcial abonado).
        if (in_array($item->payment_status, ['PAID', 'DEBT'], true)
            && ($rentPayment['payment'] ?? 0) > 0) {
            $payment = $item->payments()->create([
                'date_of_payment' => date('Y-m-d'),
                'payment_method_type_id' => $rentPayment['payment_method_type_id'],
                'reference' => $rentPayment['reference'] ?? null,
                'payment' => $rentPayment['payment'],
            ]);

            $this->linkReservationPaymentToOpenCash($payment, $rentPayment['payment_destination_id'] ?? null);
        }
    }

    /**
     * Aplica el estado de pago elegido al editar la reserva.
     *
     * Antes el formulario permitía marcar la reserva como "Pagado", pero el
     * update no registraba nada: la reserva seguía sin pagos y por eso la barra
     * del calendario conservaba su color (el color sale de la deuda real,
     * ver computeReservationPayment). Ahora:
     *   - PAID    → se registra el saldo pendiente completo y los items quedan PAID.
     *   - ADVANCE → se registra el monto indicado (pago parcial); sigue en DEBT.
     *   - DEBT / otros → no se toca nada: un pago ya registrado no se revierte
     *     desde aquí (para eso está la reversión de pagos del checkout).
     */
    private function applyReservationPaymentUpdate(HotelRent $rent, $paymentStatus, $rentPayment = [])
    {
        $paymentStatus = $paymentStatus ? strtoupper(trim($paymentStatus)) : null;

        if (!in_array($paymentStatus, ['PAID', 'ADVANCE'], true)) {
            return;
        }

        $rentPayment = is_array($rentPayment) ? $rentPayment : [];

        $rent->load('items');
        $debt = (float) $this->computeReservationPayment($rent)['debt'];

        // Ya está saldada: solo reconciliar el estado de los items para que el
        // calendario y el checkout la muestren como pagada.
        if ($debt <= 0.009) {
            if ($paymentStatus === 'PAID') {
                $this->markReservationAsPaid($rent);
            }
            return;
        }

        $amount = $paymentStatus === 'PAID'
            ? $debt
            : round((float) ($rentPayment['payment'] ?? 0), 2);

        if ($amount <= 0) {
            return;
        }

        $amount = min($amount, $debt);

        // El pago se cuelga del item de habitación vigente (el más reciente).
        $item = $rent->items()->where('type', 'HAB')->orderBy('id', 'desc')->first()
            ?: $rent->items()->where('type', '!=', 'PAY')->orderBy('id', 'desc')->first();

        if (!$item) {
            return;
        }

        $payment = $item->payments()->create([
            'date_of_payment'        => date('Y-m-d'),
            'payment_method_type_id' => $rentPayment['payment_method_type_id'] ?? '01',
            'reference'              => $rentPayment['reference'] ?? null,
            'payment'                => $amount,
        ]);

        $this->linkReservationPaymentToOpenCash($payment, $rentPayment['payment_destination_id'] ?? null);

        if ($paymentStatus === 'PAID') {
            $this->markReservationAsPaid($rent);
        }
    }

    /**
     * Marca la reserva y sus cargos como pagados.
     */
    private function markReservationAsPaid(HotelRent $rent)
    {
        HotelRentItem::where('hotel_rent_id', $rent->id)
            ->where('type', '!=', 'PAY')
            ->where('payment_status', '!=', 'PAID')
            ->update(['payment_status' => 'PAID']);

        $rent->payment_status = 'PAID';
        $rent->save();
    }

    /**
     * Registra el pago de la reserva en la caja abierta.
     *
     * La caja suma los pagos de hotel por su `global_payment`
     * (ver Cash::getHotelRentIncome), así que un pago sin este enlace nunca
     * aparece en caja chica. Mismo criterio que
     * HotelRentController::linkRentPaymentToOpenCash.
     */
    private function linkReservationPaymentToOpenCash(HotelRentItemPayment $payment, $paymentDestinationId = null)
    {
        if (empty($paymentDestinationId)) {
            $paymentDestinationId = 'cash';
        }

        // Sin caja abierta no se registra el movimiento, pero tampoco se
        // interrumpe la operación de la reserva.
        if ($paymentDestinationId === 'cash') {
            $cash = $this->getCash();
            if (!$cash || empty($cash['cash_id'])) {
                return;
            }
        }

        // Evitar duplicar el pago global al reprocesar.
        $payment->load('global_payment');
        if ($payment->global_payment) {
            $payment->global_payment()->delete();
        }

        $this->createGlobalPayment($payment, [
            'payment_destination_id' => $paymentDestinationId,
        ]);
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

        // Obtener pagos de hotel_rent_items para la fecha específica, solo de la
        // sucursal actual (se une con hotel_rooms para filtrar por sucursal).
        $total = HotelRentItem::join('hotel_rent_item_payments', 'hotel_rent_items.id', '=', 'hotel_rent_item_payments.hotel_rent_item_id')
            ->join('hotel_rents', 'hotel_rent_items.hotel_rent_id', '=', 'hotel_rents.id')
            ->join('hotel_rooms', 'hotel_rents.hotel_room_id', '=', 'hotel_rooms.id')
            ->where('hotel_rooms.establishment_id', $this->currentEstablishmentId())
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
            ->where('hotel_rooms.establishment_id', $this->currentEstablishmentId())
            ->whereDate('hotel_rent_item_payments.date_of_payment', $date)
            ->where('hotel_rooms.hotel_category_id', $categoryId)
            ->sum('hotel_rent_item_payments.payment');

        return response()->json([
            'total' => $total ?? 0
        ]);
    }

    /**
     * Totales de ventas para TODO el rango visible en una sola petición.
     *
     * Antes el frontend pedía un endpoint por cada día (totales generales) y
     * otro por cada día × categoría, generando ~16 + categorías×16 peticiones
     * HTTP por cada render del calendario. Esto causaba el retardo grande al
     * cargar. Aquí se resuelve todo con 2 consultas agrupadas.
     */
    public function getSalesTotals(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
        ]);

        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        $establishmentId = $this->currentEstablishmentId();

        // Totales generales por día (solo sucursal actual)
        $daily = HotelRentItem::join('hotel_rent_item_payments', 'hotel_rent_items.id', '=', 'hotel_rent_item_payments.hotel_rent_item_id')
            ->join('hotel_rents', 'hotel_rent_items.hotel_rent_id', '=', 'hotel_rents.id')
            ->join('hotel_rooms', 'hotel_rents.hotel_room_id', '=', 'hotel_rooms.id')
            ->where('hotel_rooms.establishment_id', $establishmentId)
            ->whereBetween('hotel_rent_item_payments.date_of_payment', [$startDate, $endDate])
            ->groupBy('day')
            ->selectRaw('DATE(hotel_rent_item_payments.date_of_payment) as day, SUM(hotel_rent_item_payments.payment) as total')
            ->pluck('total', 'day');

        // Totales por categoría y día (solo sucursal actual)
        $byCategoryRows = HotelRentItem::join('hotel_rent_item_payments', 'hotel_rent_items.id', '=', 'hotel_rent_item_payments.hotel_rent_item_id')
            ->join('hotel_rents', 'hotel_rent_items.hotel_rent_id', '=', 'hotel_rents.id')
            ->join('hotel_rooms', 'hotel_rents.hotel_room_id', '=', 'hotel_rooms.id')
            ->where('hotel_rooms.establishment_id', $establishmentId)
            ->whereBetween('hotel_rent_item_payments.date_of_payment', [$startDate, $endDate])
            ->groupBy('day', 'hotel_rooms.hotel_category_id')
            ->selectRaw('DATE(hotel_rent_item_payments.date_of_payment) as day, hotel_rooms.hotel_category_id as category_id, SUM(hotel_rent_item_payments.payment) as total')
            ->get();

        $byCategory = [];
        foreach ($byCategoryRows as $row) {
            $byCategory["{$row->day}_{$row->category_id}"] = (float) $row->total;
        }

        return response()->json([
            'daily'       => $daily,
            'by_category' => $byCategory,
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
