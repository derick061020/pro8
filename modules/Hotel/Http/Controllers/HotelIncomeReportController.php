<?php

namespace Modules\Hotel\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Cash;
use App\Models\Tenant\DocumentPayment;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\SaleNotePayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Finance\Models\GlobalPayment;
use Modules\Finance\Models\IncomePayment;
use Modules\Hotel\Models\HotelRentItemPayment;

/**
 * Hotel → Ingresos diarios (sólo administradores).
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

        // Una caja puede quedar abierta varios días: entra si estuvo abierta en
        // algún momento del periodo, y de ella sólo cuentan los pagos del periodo.
        $cashes = Cash::query()
            ->with('user:id,name')
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

        ksort($days);
        $days = collect($days)->map(function ($d) {
            return [
                'date' => $d['date'],
                'turns' => count($d['cashes']),
                'rooms' => array_keys($d['rooms']),
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

        return [
            'room' => null,
            'label' => trim($kind.' '.($record ? ($record->series ?? '').'-'.($record->number ?? '') : ''), ' -'),
            'customer' => $customerName,
        ];
    }

    private function isAdmin(): bool
    {
        return optional(auth()->user())->type === 'admin';
    }
}
