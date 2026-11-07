<?php

namespace Modules\Hotel\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Cash;
use App\Models\Tenant\DocumentPayment;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\SaleNote;
use App\Models\Tenant\SaleNotePayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\GlobalPayment;
use Modules\Finance\Models\IncomePayment;
use Modules\Hotel\Exports\HotelIncomeExport;
use Modules\Hotel\Models\HotelRent;
use Modules\Hotel\Models\HotelRentItemPayment;
use Modules\Hotel\Models\HotelRoom;

/**
 * Finanzas → Ingresos diarios (permiso "Ingresos diarios" del usuario).
 *
 * Cuadro de cuánto dinero entró por cada turno de caja y cada día, separado por
 * método de pago (efectivo, Yape, Plin, tarjeta…), con el monto con el que abrió
 * cada cajero. No depende de los comprobantes: suma los pagos registrados en la
 * caja, con la misma regla que el cierre de caja ("el pago de hotel manda"):
 * los comprobantes ligados a una estadía se excluyen porque su dinero ya está en
 * el pago del hotel.
 */
class HotelIncomeReportController extends Controller
{
    /** Comprobantes anulados o rechazados no son ingreso */
    private const VOID_STATES = ['09', '11', '13'];

    public function index()
    {
        abort_unless($this->isAdmin(), 403);

        return view('hotel::reports.daily_income');
    }

    public function data(Request $request)
    {
        abort_unless($this->isAdmin(), 403);

        $from = Carbon::parse($request->input('from', now()->format('Y-m-d')))->startOfDay();
        $to = Carbon::parse($request->input('to', $from->format('Y-m-d')))->endOfDay();
        if ($to->lt($from)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }
        // Rango máximo razonable para que la consulta no se vuelva pesada
        if ($from->diffInDays($to) > 62) {
            $from = $to->copy()->subDays(62)->startOfDay();
        }

        $methods = PaymentMethodType::query()->get(['id', 'description'])->keyBy('id');

        // Sucursal: una en particular o todas ('all'). Por defecto la del usuario.
        $establishments = Establishment::query()->orderBy('id')->get(['id', 'description', 'address']);
        $establishmentId = $request->input('establishment_id', optional(auth()->user())->establishment_id);
        if ($establishmentId !== 'all' && !$establishments->contains('id', (int) $establishmentId)) {
            $establishmentId = 'all';
        }
        $establishmentId = $establishmentId === 'all' ? null : (int) $establishmentId;

        // Una caja puede quedar abierta varios días: entra si estuvo abierta en
        // algún momento del periodo, y de ella sólo cuentan los pagos del periodo.
        // La caja no guarda la sucursal: se toma la del cajero que la abrió
        $cashes = Cash::query()
            ->with('user:id,name,establishment_id')
            ->when($establishmentId, function ($q) use ($establishmentId) {
                $q->whereHas('user', function ($u) use ($establishmentId) {
                    $u->where('establishment_id', $establishmentId);
                });
            })
            ->where('date_opening', '<=', $to->format('Y-m-d'))
            ->where(function ($q) use ($from) {
                $q->whereNull('date_closed')->orWhere('date_closed', '>=', $from->format('Y-m-d'));
            })
            ->orderBy('date_opening')
            ->orderBy('time_opening')
            ->get();

        $payments = GlobalPayment::query()
            ->where('destination_type', Cash::class)
            ->whereIn('destination_id', $cashes->pluck('id'))
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('payment_type', [
                DocumentPayment::class,
                SaleNotePayment::class,
                HotelRentItemPayment::class,
                IncomePayment::class,
            ])
            ->with('payment')
            ->get()
            ->each(function ($global) {
                // Para el detalle: habitación y huésped de los pagos del hotel
                if ($global->payment instanceof HotelRentItemPayment) {
                    $global->payment->loadMissing('associated_record_payment.hotel_rent.room');
                }
            })
            ->groupBy('destination_id');

        $usedMethods = [];
        $turns = [];
        $days = [];

        foreach ($cashes as $cash) {
            $byMethod = [];
            $count = 0;
            $detail = [];
            $rooms = [];

            foreach ($payments->get($cash->id, collect()) as $global) {
                $amount = $this->incomeAmount($global);
                if ($amount <= 0) {
                    continue;
                }
                $methodId = $global->payment->payment_method_type_id ?? 'otros';
                $byMethod[$methodId] = round(($byMethod[$methodId] ?? 0) + $amount, 2);
                $usedMethods[$methodId] = true;
                $count++;

                $concept = $this->concept($global->payment);
                if ($concept['room']) {
                    $rooms[$concept['room']] = true;
                }
                $day = optional($global->created_at)->format('Y-m-d');
                if (!isset($days[$day])) {
                    $days[$day] = ['date' => $day, 'cashes' => [], 'rooms' => [], 'by_method' => []];
                }
                $days[$day]['cashes'][$cash->id] = true;
                if ($concept['room']) {
                    $days[$day]['rooms'][$concept['room']] = true;
                }
                $days[$day]['by_method'][$methodId] = round(($days[$day]['by_method'][$methodId] ?? 0) + $amount, 2);

                $detail[] = [
                    'id' => $global->id,
                    // Sólo los pagos de habitación se quitan desde aquí; un comprobante
                    // o nota de venta se anula en su propio módulo
                    'removable' => $global->payment instanceof HotelRentItemPayment,
                    'time' => optional($global->payment->date_of_payment ?? $global->created_at)->format('Y-m-d H:i') ?? null,
                    'created_at' => optional($global->created_at)->format('Y-m-d H:i'),
                    'room' => $concept['room'],
                    'concept' => $concept['label'],
                    'customer' => $concept['customer'],
                    'method_id' => $methodId,
                    'amount' => $amount,
                ];
            }
            usort($detail, function ($a, $b) {
                return strcmp((string) $a['created_at'], (string) $b['created_at']);
            });

            $turns[] = [
                'id' => $cash->id,
                'date' => $cash->date_opening,
                'cashier' => optional($cash->user)->name,
                'establishment' => optional($establishments->firstWhere('id', optional($cash->user)->establishment_id))->description,
                'opened_at' => trim($cash->date_opening.' '.$cash->time_opening),
                'closed_at' => $cash->date_closed ? trim($cash->date_closed.' '.$cash->time_closed) : null,
                'open' => (bool) $cash->state,
                'beginning_balance' => round((float) $cash->beginning_balance, 2),
                'by_method' => $byMethod,
                'total' => round(array_sum($byMethod), 2),
                'payments_count' => $count,
                'rooms' => array_keys($rooms),
                'detail' => $detail,
            ];
        }

        // Efectivo primero, después el resto en el orden del catálogo
        $methodIds = array_keys($usedMethods);
        usort($methodIds, function ($a, $b) use ($methods) {
            $rank = function ($id) use ($methods) {
                $d = mb_strtolower(optional($methods->get($id))->description ?? '');
                return str_contains($d, 'efectivo') ? 0 : 1;
            };
            return [$rank($a), (string) $a] <=> [$rank($b), (string) $b];
        });

        // Habitaciones ocupadas cada día, hayan pagado ese día o no (una estadía de
        // varias noches pagada al entrar sólo aparece como "cobrada" el primer día).
        $occupied = $this->occupiedRoomsByDay($from, $to, $establishmentId);
        foreach (array_keys($occupied) as $day) {
            if (!isset($days[$day])) {
                $days[$day] = ['date' => $day, 'cashes' => [], 'rooms' => [], 'by_method' => []];
            }
        }

        ksort($days);
        $days = collect($days)->map(function ($d) use ($occupied) {
            return [
                'date' => $d['date'],
                'turns' => count($d['cashes']),
                'rooms' => array_keys($d['rooms']),
                'occupied' => $occupied[$d['date']] ?? [],
                'by_method' => $d['by_method'],
                'total' => round(array_sum($d['by_method']), 2),
            ];
        })->values();

        // Turnos sin pagos en el periodo y que no se abrieron en él no aportan
        $turns = array_values(array_filter($turns, function ($t) use ($from, $to) {
            return $t['payments_count'] > 0 || ($t['date'] >= $from->format('Y-m-d') && $t['date'] <= $to->format('Y-m-d'));
        }));

        return [
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'methods' => collect($methodIds)->map(function ($id) use ($methods) {
                return ['id' => $id, 'description' => optional($methods->get($id))->description ?? 'Otros'];
            })->values(),
            'turns' => $turns,
            'days' => $days,
            'establishments' => $establishments->map(function ($e) {
                return ['id' => $e->id, 'description' => $e->description];
            })->values(),
            'establishment_id' => $establishmentId ?? 'all',
            'rooms_now' => $this->roomsNow($establishmentId),
            'totals' => [
                'by_method' => collect($turns)->reduce(function ($acc, $t) {
                    foreach ($t['by_method'] as $id => $amount) {
                        $acc[$id] = round(($acc[$id] ?? 0) + $amount, 2);
                    }
                    return $acc;
                }, []),
                'total' => round(collect($turns)->sum('total'), 2),
            ],
        ];
    }

    /** Habitaciones ocupadas en este momento (estado de recepción), con sus nombres */
    private function roomsNow(?int $establishmentId): array
    {
        $rooms = HotelRoom::query()
            ->with('establishment:id,description')
            ->where('active', true)
            ->when($establishmentId, function ($q) use ($establishmentId) { $q->where('establishment_id', $establishmentId); })
            ->get(['id', 'name', 'status', 'establishment_id']);
        $occupied = $rooms->where('status', 'OCUPADO');

        return [
            'occupied' => $occupied->count(),
            'total' => $rooms->count(),
            'names' => $this->roomNames($occupied->all()),
        ];
    }

    /**
     * Nombres de habitación ordenados; si un número se repite entre sucursales se
     * le agrega la sucursal ("5 (HOSPEDAJE BUENDIA I)").
     *
     * @param  array<HotelRoom> $rooms
     * @return list<string>
     */
    private function roomNames(array $rooms): array
    {
        $repeated = collect($rooms)->countBy('name')->filter(function ($n) { return $n > 1; });
        $names = collect($rooms)->map(function ($room) use ($repeated) {
            return $repeated->has($room->name)
                ? $room->name.' ('.(optional($room->establishment)->description ?? 'sucursal').')'
                : (string) $room->name;
        })->values()->all();
        natsort($names);

        return array_values($names);
    }

    /**
     * Por cada día del periodo, las habitaciones con una estadía en curso ese día
     * (entrada <= día <= salida; no cuenta reservas sin check-in).
     *
     * @return array<string, list<string>> fecha => nombres de habitación
     */
    private function occupiedRoomsByDay(Carbon $from, Carbon $to, ?int $establishmentId = null): array
    {
        $rents = HotelRent::query()
            ->when($establishmentId, function ($q) use ($establishmentId) {
                $q->where('establishment_id', $establishmentId);
            })
            ->with('room:id,name,establishment_id', 'room.establishment:id,description')
            ->where(function ($q) {
                $q->where('is_reserve', false)->orWhereNull('is_reserve');
            })
            ->where('input_date', '<=', $to->format('Y-m-d'))
            ->where('output_date', '>=', $from->format('Y-m-d'))
            ->get(['id', 'hotel_room_id', 'input_date', 'output_date']);

        $result = [];
        for ($day = $from->copy()->startOfDay(); $day->lte($to); $day->addDay()) {
            $date = $day->format('Y-m-d');
            $rooms = [];
            foreach ($rents as $rent) {
                $in = substr((string) $rent->getRawOriginal('input_date'), 0, 10);
                $out = substr((string) $rent->getRawOriginal('output_date'), 0, 10);
                if ($in <= $date && $out >= $date && $rent->room) {
                    // Por id: la principal y la sucursal tienen habitaciones con el mismo número
                    $rooms[$rent->room->id] = $rent->room;
                }
            }
            if ($rooms) {
                $result[$date] = $this->roomNames(array_values($rooms));
            }
        }

        return $result;
    }

    /**
     * Quitar un pago de habitación registrado por error (p. ej. pusieron mal el
     * precio, finalizaron la estadía y la volvieron a registrar con el precio
     * correcto: el primer pago seguía sumando). Hace lo mismo que "Revertir pago"
     * del checkout: borra el pago y su movimiento de caja, y el item vuelve a
     * quedar pendiente.
     */
    public function removePayment($globalId)
    {
        abort_unless($this->isAdmin(), 403);

        $global = GlobalPayment::query()->findOrFail($globalId);
        $payment = $global->payment;
        if (!$payment instanceof HotelRentItemPayment) {
            return response()->json([
                'success' => false,
                'message' => 'Sólo se pueden quitar pagos de habitaciones. Un comprobante o nota de venta se anula desde su listado.',
            ], 422);
        }

        DB::connection('tenant')->transaction(function () use ($global, $payment) {
            $item = $payment->associated_record_payment;
            Log::info('Ingresos diarios: pago de hotel quitado', [
                'user_id' => auth()->id(),
                'payment_id' => $payment->id,
                'amount' => $payment->payment,
                'hotel_rent_item_id' => $payment->hotel_rent_item_id,
                'hotel_rent_id' => optional($item)->hotel_rent_id,
            ]);
            $global->delete();
            $payment->delete();
            if ($item && $item->payment_status === 'PAID') {
                $item->payment_status = 'PENDING';
                $item->save();
            }
        });

        return ['success' => true, 'message' => 'Pago quitado.'];
    }

    /** Excel con los mismos datos que la pantalla */
    public function export(Request $request)
    {
        abort_unless($this->isAdmin(), 403);

        $report = $this->data($request);

        return (new HotelIncomeExport)
            ->report($report)
            ->download('ingresos-diarios-'.$report['from'].'-a-'.$report['to'].'.xlsx');
    }

    /** Monto que cuenta como ingreso de ese pago (0 si no corresponde) */
    private function incomeAmount(GlobalPayment $global): float
    {
        $payment = $global->payment;
        if (!$payment) {
            return 0;
        }

        if ($payment instanceof DocumentPayment || $payment instanceof SaleNotePayment) {
            $record = $payment->associated_record_payment;
            // Comprobante de una estadía: ese dinero ya está en el pago del hotel
            if (!$record || $record->hotel_rent_id || in_array($record->state_type_id, self::VOID_STATES, true)) {
                return 0;
            }
            // Factura/boleta generada desde una nota de venta: el dinero ya se contó
            // con la nota (o con el pago de la habitación, si la nota es del hotel).
            // El comprobante nuevo no guarda la estadía y se contaba dos veces.
            if ($payment instanceof DocumentPayment && $record->sale_note_id) {
                $note = SaleNote::query()->find($record->sale_note_id);
                if ($note && ($note->hotel_rent_id
                    || (!in_array($note->state_type_id, self::VOID_STATES, true) && $note->payments()->sum('payment') > 0))) {
                    return 0;
                }
            }
        }

        if ($payment instanceof HotelRentItemPayment && !$payment->associated_record_payment) {
            return 0;
        }

        return (float) $payment->payment;
    }

    /**
     * Qué se cobró: la habitación y el huésped (pagos del hotel), o la serie y el
     * número de la nota de venta / comprobante.
     *
     * @return array{room: ?string, label: string, customer: ?string}
     */
    private function concept($payment): array
    {
        if ($payment instanceof HotelRentItemPayment) {
            $item = $payment->associated_record_payment;
            $rent = optional($item)->hotel_rent;
            $room = optional(optional($rent)->room)->name;
            $customer = optional($rent)->customer;
            $customerName = is_object($customer) ? ($customer->name ?? null) : (is_array($customer) ? ($customer['name'] ?? null) : null);
            $type = optional($item)->type;
            $what = $type === 'HAB' ? 'Habitación' : ($type === 'PRO' ? 'Productos' : 'Hotel');

            return [
                'room' => $room ? (string) $room : null,
                'label' => trim($what.($room ? ' '.$room : '')),
                'customer' => $customerName,
            ];
        }

        $record = $payment->associated_record_payment ?? null;
        $kind = $payment instanceof SaleNotePayment ? 'Nota de venta' : ($payment instanceof DocumentPayment ? 'Comprobante' : 'Otro ingreso');
        $customer = optional($record)->customer;
        $customerName = is_object($customer) ? ($customer->name ?? null) : (is_array($customer) ? ($customer['name'] ?? null) : null);

        // Habitación del comprobante: la de su estadía, o la de la nota de venta de la que salió
        $rentId = optional($record)->hotel_rent_id;
        if (!$rentId && $payment instanceof DocumentPayment && optional($record)->sale_note_id) {
            $rentId = optional(SaleNote::query()->find($record->sale_note_id, ['id', 'hotel_rent_id']))->hotel_rent_id;
        }
        $room = $rentId ? optional(optional(HotelRent::query()->with('room:id,name')->find($rentId, ['id', 'hotel_room_id']))->room)->name : null;

        return [
            'room' => $room ? (string) $room : null,
            'label' => trim($kind.' '.($record ? ($record->series ?? '').'-'.($record->number ?? '') : ''), ' -'),
            'customer' => $customerName,
        ];
    }

    /**
     * Permiso "Ingresos diarios" de Finanzas (Usuarios → Permisos). Si el
     * inquilino todavía no tiene ese permiso creado, sólo los administradores.
     */
    private function isAdmin(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        $levelId = DB::connection('tenant')->table('module_levels')->where('value', 'finances_daily_income')->value('id');
        if (!$levelId) {
            return $user->type === 'admin';
        }

        return DB::connection('tenant')->table('module_level_user')
            ->where('user_id', $user->id)->where('module_level_id', $levelId)->exists();
    }
}
