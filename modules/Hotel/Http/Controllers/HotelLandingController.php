<?php

namespace Modules\Hotel\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use DB;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Configuration;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\HotelRent;
use Modules\Hotel\Models\HotelRentOrder;
use Modules\Hotel\Models\HotelRentItem;

/**
 * Landing pública de reservas de hotel.
 *
 * Estas rutas NO requieren autenticación: están pensadas para que un
 * huésped externo pueda ver las habitaciones del hotel (del tenant actual)
 * y enviar una solicitud de reserva. Cada tenant resuelve su propia base de
 * datos por el dominio, así que la misma landing sirve a todos los tenants
 * mostrando, en cada caso, sus propias habitaciones.
 */
class HotelLandingController extends Controller
{
    /**
     * Render de la landing con las habitaciones del hotel.
     */
    public function index()
    {
        $establishment = Establishment::first();
        $configuration = Configuration::first();

        $rooms = $this->availableRoomsCollection();

        return view('hotel::landing.index', compact('establishment', 'configuration', 'rooms'));
    }

    /**
     * Consulta de disponibilidad para un rango de fechas (AJAX).
     */
    public function availability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'input_date'  => 'required|date_format:Y-m-d',
            'output_date' => 'required|date_format:Y-m-d|after_or_equal:input_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Indica fechas de entrada y salida válidas.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $start = Carbon::parse($request->input_date . ' 14:00');
        $end   = Carbon::parse($request->output_date . ' 12:00');

        $rooms = $this->availableRoomsCollection()
            ->filter(function ($room) use ($start, $end) {
                return $room['status'] !== 'MANTENIMIENTO'
                    && !HotelRent::findOverlappingRent($room['id'], $start, $end);
            })
            ->values();

        return response()->json([
            'success' => true,
            'data'    => $rooms,
        ]);
    }

    /**
     * Registrar una solicitud de reserva enviada desde la landing.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel_room_id'   => 'required|numeric',
            'customer_name'   => 'required|string|max:255',
            'customer_email'  => 'required|email|max:255',
            'customer_phone'  => 'required|string|max:30',
            'customer_doc'    => 'nullable|string|max:20',
            'input_date'      => 'required|date_format:Y-m-d',
            'output_date'     => 'required|date_format:Y-m-d|after_or_equal:input_date',
            'adults'          => 'required|numeric|min:1',
            'children'        => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string|max:250',
            'hotel_rate_id'   => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Revisa los datos del formulario.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::connection('tenant')->beginTransaction();
        try {
            $room = HotelRoom::with('category', 'rates.rate')->findOrFail($request->hotel_room_id);

            if (!$room->active) {
                DB::connection('tenant')->rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'La habitación seleccionada no está disponible.',
                ], 422);
            }

            if ($room->status === 'MANTENIMIENTO') {
                DB::connection('tenant')->rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'La habitación está en mantenimiento y no puede reservarse.',
                ], 422);
            }

            $inputTime  = '14:00';
            $outputTime = '12:00';
            $newStart = Carbon::parse($request->input_date . ' ' . $inputTime);
            $newEnd   = Carbon::parse($request->output_date . ' ' . $outputTime);

            $conflict = HotelRent::findOverlappingRent($room->id, $newStart, $newEnd);
            if ($conflict) {
                DB::connection('tenant')->rollBack();
                $cStart = Carbon::parse($conflict->input_date . ' ' . ($conflict->input_time ?: '14:00'))->format('d/m/Y');
                $cEnd   = Carbon::parse($conflict->output_date . ' ' . ($conflict->output_time ?: '12:00'))->format('d/m/Y');
                return response()->json([
                    'success' => false,
                    'message' => "La habitación ya está reservada del {$cStart} al {$cEnd}. Elige otras fechas.",
                ], 422);
            }

            // Tarifa: la indicada o la primera disponible de la habitación
            $roomRate = $request->hotel_rate_id
                ? $room->rates->firstWhere('hotel_rate_id', (int) $request->hotel_rate_id)
                : $room->rates->first();

            $nights = max(1, $newStart->copy()->startOfDay()->diffInDays($newEnd->copy()->startOfDay()));
            $unitPrice = (float) ($roomRate->price ?? 0);
            $total = round($unitPrice * $nights, 2);

            $adults   = (int) $request->adults;
            $children = (int) ($request->children ?? 0);

            $customer = [
                'name'      => $request->customer_name,
                'email'     => $request->customer_email,
                'telephone' => $request->customer_phone,
                'number'    => $request->customer_doc,
                'address'   => null,
            ];

            $rent = HotelRent::create([
                'customer_id'      => 0, // huésped externo (sin ficha de cliente)
                'customer'         => $customer,
                'notes'            => $request->notes,
                'adults'           => $adults,
                'children'         => $children,
                'towels'           => 1,
                'hotel_room_id'    => $room->id,
                'hotel_rate_id'    => $roomRate->hotel_rate_id ?? null,
                'duration'         => $nights,
                'quantity_persons' => $adults + $children,
                'data_persons'     => null,
                'payment_status'   => 'DEBT',
                'input_date'       => $request->input_date,
                'input_time'       => $inputTime,
                'output_date'      => $request->output_date,
                'output_time'      => $outputTime,
                'is_reserve'       => true,
                'status'           => 'ACTIVE',
                'establishment_id' => $room->establishment_id,
            ]);

            $order = new HotelRentOrder();
            $order->hotel_rent_id    = $rent->id;
            $order->order_number     = 1;
            $order->order_status     = 'DEBT';
            $order->establishment_id = $room->establishment_id;
            $order->save();

            $description = trim(($room->category->description ?? 'Habitación') . ' ' . $room->name);

            $productJson = [
                'item_id'    => $room->item_id,
                'description' => $description,
                'quantity'   => $nights,
                'unit_price' => $unitPrice,
                'total'      => $total,
                'item'       => [
                    'description'      => $description,
                    'name_product_pdf' => $description,
                    'unit_price'       => $unitPrice,
                ],
            ];

            $item = new HotelRentItem();
            $item->type                = 'HAB';
            $item->hotel_rent_id       = $rent->id;
            $item->item_id             = $room->item_id;
            $item->item                = $productJson;
            $item->quantity            = $nights;
            $item->unit_price          = $unitPrice;
            $item->total               = $total;
            $item->payment_status      = 'DEBT';
            $item->hotel_rent_order_id = $order->id;
            $item->save();

            DB::connection('tenant')->commit();

            return response()->json([
                'success' => true,
                'message' => '¡Reserva registrada! El hotel se pondrá en contacto contigo para confirmar.',
                'data'    => [
                    'id'     => $rent->id,
                    'room'   => $description,
                    'nights' => $nights,
                    'total'  => $total,
                ],
            ]);
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'No se pudo registrar la reserva: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Colección normalizada de habitaciones activas con su precio mínimo.
     */
    private function availableRoomsCollection()
    {
        return HotelRoom::with('category', 'rates.rate')
            ->where('active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($room) {
                $rates = $room->rates->map(function ($rr) {
                    return [
                        'hotel_rate_id' => $rr->hotel_rate_id,
                        'description'   => $rr->rate->description ?? 'Tarifa',
                        'price'         => (float) $rr->price,
                    ];
                })->values();

                $minPrice = $rates->min('price');

                return [
                    'id'          => $room->id,
                    'name'        => $room->name,
                    'category'    => $room->category->description ?? 'Habitación',
                    'description' => $room->description,
                    'status'      => $room->status,
                    'rates'       => $rates,
                    'min_price'   => $minPrice ?? 0,
                ];
            });
    }
}
