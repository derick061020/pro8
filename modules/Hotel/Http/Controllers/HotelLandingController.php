<?php

namespace Modules\Hotel\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Configuration;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\HotelRent;
use Modules\Hotel\Models\HotelRentOrder;
use Modules\Hotel\Models\HotelRentItem;

/**
 * Landing pública de reservas de hotel.
 *
 * Sirve el template original (idéntico) y lo conecta con la base de datos del
 * tenant actual: las habitaciones se listan dinámicamente y el formulario de
 * reserva crea un registro real (HotelRent) que aparece en recepción/calendario.
 *
 * Las rutas NO requieren autenticación. Cada tenant resuelve su propia base de
 * datos por el dominio, así que la misma landing sirve a todos los tenants.
 */
class HotelLandingController extends Controller
{
    /**
     * Render de la landing (template original) con las habitaciones reales.
     */
    public function index()
    {
        $establishment = Establishment::first();
        $configuration = Configuration::first();
        $rooms = $this->roomsCollection();

        return view('hotel::landing.index', compact('establishment', 'configuration', 'rooms'));
    }

    /**
     * Registrar una solicitud de reserva enviada desde el formulario del template.
     *
     * Devuelve un fragmento HTML (alert de Bootstrap) que el JS inserta en
     * #message, igual que hacía el php/reservation.php original.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'room'     => 'required|numeric',
            'checkin'  => 'required',
            'checkout' => 'required',
            'adults'   => 'nullable|numeric|min:1',
            'children' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->alert('danger', 'Por favor completa correo, habitación y fechas de entrada/salida.');
        }

        // El datepicker del template usa formato dd/mm/yyyy
        $inDate  = $this->parseDate($request->checkin);
        $outDate = $this->parseDate($request->checkout);

        if (!$inDate || !$outDate) {
            return $this->alert('danger', 'Las fechas no tienen un formato válido.');
        }
        if ($outDate->lte($inDate)) {
            return $this->alert('danger', 'La fecha de salida debe ser posterior a la de entrada.');
        }

        DB::connection('tenant')->beginTransaction();
        try {
            $room = HotelRoom::with('category', 'rates.rate')->find($request->room);

            if (!$room || !$room->active) {
                DB::connection('tenant')->rollBack();
                return $this->alert('danger', 'La habitación seleccionada no está disponible.');
            }
            if ($room->status === 'MANTENIMIENTO') {
                DB::connection('tenant')->rollBack();
                return $this->alert('danger', 'La habitación está en mantenimiento y no puede reservarse.');
            }

            $inputTime  = '14:00';
            $outputTime = '12:00';
            $newStart = Carbon::parse($inDate->format('Y-m-d') . ' ' . $inputTime);
            $newEnd   = Carbon::parse($outDate->format('Y-m-d') . ' ' . $outputTime);

            $conflict = HotelRent::findOverlappingRent($room->id, $newStart, $newEnd);
            if ($conflict) {
                DB::connection('tenant')->rollBack();
                $cStart = Carbon::parse($conflict->input_date . ' ' . ($conflict->input_time ?: '14:00'))->format('d/m/Y');
                $cEnd   = Carbon::parse($conflict->output_date . ' ' . ($conflict->output_time ?: '12:00'))->format('d/m/Y');
                return $this->alert('danger', "La habitación ya está reservada del {$cStart} al {$cEnd}. Elige otras fechas.");
            }

            $roomRate  = $room->rates->first();
            $nights    = max(1, $inDate->copy()->startOfDay()->diffInDays($outDate->copy()->startOfDay()));
            $unitPrice = (float) ($roomRate->price ?? 0);
            $total     = round($unitPrice * $nights, 2);

            $adults   = (int) ($request->adults ?? 1);
            $children = (int) ($request->children ?? 0);

            $customer = [
                'name'      => $request->email,
                'email'     => $request->email,
                'telephone' => null,
                'number'    => null,
                'address'   => null,
            ];

            $rent = HotelRent::create([
                'customer_id'      => 0, // huésped externo (sin ficha de cliente)
                'customer'         => $customer,
                'notes'            => 'Reserva web (landing)',
                'adults'           => $adults,
                'children'         => $children,
                'towels'           => 1,
                'hotel_room_id'    => $room->id,
                'hotel_rate_id'    => $roomRate->hotel_rate_id ?? null,
                'duration'         => $nights,
                'quantity_persons' => $adults + $children,
                'data_persons'     => null,
                'payment_status'   => 'DEBT',
                'input_date'       => $inDate->format('Y-m-d'),
                'input_time'       => $inputTime,
                'output_date'      => $outDate->format('Y-m-d'),
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

            $item = new HotelRentItem();
            $item->type                = 'HAB';
            $item->hotel_rent_id       = $rent->id;
            $item->item_id             = $room->item_id;
            $item->item                = [
                'item_id'     => $room->item_id,
                'description' => $description,
                'quantity'    => $nights,
                'unit_price'  => $unitPrice,
                'total'       => $total,
                'item'        => [
                    'description'      => $description,
                    'name_product_pdf' => $description,
                    'unit_price'       => $unitPrice,
                ],
            ];
            $item->quantity            = $nights;
            $item->unit_price          = $unitPrice;
            $item->total               = $total;
            $item->payment_status      = 'DEBT';
            $item->hotel_rent_order_id = $order->id;
            $item->save();

            DB::connection('tenant')->commit();

            $msg = '¡Reserva registrada para ' . e($description) . ' del '
                . $inDate->format('d/m/Y') . ' al ' . $outDate->format('d/m/Y')
                . ($total > 0 ? ' (estimado: S/ ' . number_format($total, 2) . ')' : '')
                . '. El hotel se pondrá en contacto contigo para confirmar.';

            return $this->alert('success', $msg);
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollBack();
            return $this->alert('danger', 'No se pudo registrar la reserva. Inténtalo de nuevo.');
        }
    }

    /**
     * Colección de habitaciones activas con su precio mínimo (para el select y las tarjetas).
     */
    private function roomsCollection()
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

                return [
                    'id'          => $room->id,
                    'name'        => $room->name,
                    'category'    => $room->category->description ?? 'Habitación',
                    'description' => $room->description,
                    'status'      => $room->status,
                    'min_price'   => $rates->min('price') ?? 0,
                ];
            });
    }

    /**
     * Parsear la fecha del datepicker (dd/mm/yyyy) de forma tolerante.
     */
    private function parseDate($value)
    {
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d'] as $fmt) {
            try {
                $d = Carbon::createFromFormat($fmt, trim($value));
                if ($d) {
                    return $d->startOfDay();
                }
            } catch (\Throwable $e) {
                // probar siguiente formato
            }
        }
        return null;
    }

    /**
     * Fragmento HTML de alerta, con el mismo estilo del template original.
     */
    private function alert($type, $message)
    {
        $html = '<div class="alert alert-' . $type . ' alert-dismissable">'
            . '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
            . e($message) . '</div>';

        return response($html, 200)->header('Content-Type', 'text/html');
    }
}
