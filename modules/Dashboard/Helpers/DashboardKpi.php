<?php

namespace Modules\Dashboard\Helpers;

use App\Models\Tenant\Document;
use App\Models\Tenant\SaleNote;
use App\Models\Tenant\Purchase;
use Carbon\Carbon;

/**
 * Clase DashboardKpi
 *
 * Totales de KPI para el nuevo dashboard. Replica las reglas del dashboard
 * anterior (tipos de estado aceptados, tipos de documento de venta, resta de
 * notas de crédito y conversión de moneda PEN/USD) pero calculadas con
 * agregados SQL ligeros.
 *
 * Por defecto abarca todo el negocio; pasa establishment_id para limitarlo a
 * una sola sucursal.
 *
 * @package Modules\Dashboard\Helpers
 */
class DashboardKpi
{
    private $acceptedStates = ['01', '03', '05', '07', '13'];

    private $salesDocumentTypes = ['01', '03', '08'];

    private $monthLabels = [
        1 => 'ENE', 2 => 'FEB', 3 => 'MAR', 4 => 'ABR', 5 => 'MAY', 6 => 'JUN',
        7 => 'JUL', 8 => 'AGO', 9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC',
    ];

    public function data($request)
    {
        $establishment_id = isset($request['establishment_id']) ? $request['establishment_id'] : null;
        $compare = isset($request['compare']) ? $request['compare'] : 'none';

        [$start, $end] = $this->resolveRange($request);

        // Totales para el rango seleccionado.
        $sales = $this->salesTotal($establishment_id, $start, $end);
        $purchases = $this->purchasesTotal($establishment_id, $start, $end);

        [$cmp_start, $cmp_end] = $this->resolveCompareRange($start, $end, $compare);

        $prev_sales = $cmp_start ? $this->salesTotal($establishment_id, $cmp_start, $cmp_end) : 0;
        $prev_purchases = $cmp_start ? $this->purchasesTotal($establishment_id, $cmp_start, $cmp_end) : 0;

        $year = Carbon::parse($end);
        $year_start = $year->copy()->startOfYear()->format('Y-m-d');
        $year_end = $year->copy()->endOfYear()->format('Y-m-d');

        return [
            'month_sales' => $this->formatNumber($sales),
            'month_purchases' => $this->formatNumber($purchases),
            'accumulated_sales' => $this->formatNumber($this->salesTotal($establishment_id, $year_start, $year_end)),
            'accumulated_year' => $year->format('Y'),
            'month_sales_variation' => $compare === 'none' ? 0 : $this->variation($sales, $prev_sales),
            'month_purchases_variation' => $compare === 'none' ? 0 : $this->variation($purchases, $prev_purchases),
        ];
    }

    /**
     * Resuelve el rango de fechas [inicio, fin] a partir del request. Usa el
     * mes actual cuando no se proporciona un rango explícito.
     */
    private function resolveRange($request)
    {
        $start = isset($request['date_start']) && $request['date_start'] ? $request['date_start'] : null;
        $end = isset($request['date_end']) && $request['date_end'] ? $request['date_end'] : null;

        if (!$start || !$end) {
            $now = Carbon::now();
            $start = $now->copy()->startOfMonth()->format('Y-m-d');
            $end = $now->copy()->endOfMonth()->format('Y-m-d');
        }

        return [$start, $end];
    }

    /**
     * Resuelve el rango de comparación. Devuelve [null, null] cuando no hay
     * comparación.
     */
    private function resolveCompareRange($start, $end, $compare)
    {
        if ($compare === 'previous_year') {
            return [
                Carbon::parse($start)->subYearNoOverflow()->format('Y-m-d'),
                Carbon::parse($end)->subYearNoOverflow()->format('Y-m-d'),
            ];
        }

        if ($compare === 'previous_period') {
            $days = Carbon::parse($start)->diffInDays(Carbon::parse($end)) + 1;
            $prev_end = Carbon::parse($start)->subDay();
            $prev_start = $prev_end->copy()->subDays($days - 1);

            return [$prev_start->format('Y-m-d'), $prev_end->format('Y-m-d')];
        }

        return [null, null];
    }

    /**
     * Ventas vs compras desglosadas según el período seleccionado:
     *  - today : 24 segmentos por hora (00h..23h)
     *  - week  : 7 segmentos por día (Lun..Dom)
     *  - month : segmentos semanales dentro del mes (Sem 1..N)
     *  - year  : 12 segmentos por mes (ENE..DIC)
     *
     * Por defecto abarca todo el negocio; pasa establishment_id para limitarlo
     * a una sola sucursal.
     */
    public function monthlyComparison($request)
    {
        $establishment_id = isset($request['establishment_id']) ? $request['establishment_id'] : null;
        $period = isset($request['period']) ? $request['period'] : 'month';

        [$range_start, $range_end] = $this->resolveRange($request);

        switch ($period) {
            case 'today':
                return $this->bucketsByHour($establishment_id, $range_start);
            case 'week':
                return $this->bucketsByDay($establishment_id, $range_start, $range_end);
            case 'year':
                return $this->bucketsByMonth($establishment_id, $range_start, $range_end);
            case 'month':
            default:
                return $this->bucketsByWeek($establishment_id, $range_start, $range_end);
        }
    }

    /**
     * 24 segmentos por hora para un solo día.
     */
    private function bucketsByHour($establishment_id, $day)
    {
        $categories = [];
        $sales = [];
        $purchases = [];

        for ($h = 0; $h < 24; $h++) {
            $categories[] = str_pad($h, 2, '0', STR_PAD_LEFT) . 'h';
            $sales[] = $this->salesTotal($establishment_id, $day, $day, $h);
            $purchases[] = $this->purchasesTotal($establishment_id, $day, $day, $h);
        }

        return compact('categories', 'sales', 'purchases');
    }

    /**
     * Un segmento por día a lo largo del rango [inicio, fin] (usado en la vista
     * semanal).
     */
    private function bucketsByDay($establishment_id, $start, $end)
    {
        $dayLabels = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'];

        $cursor = Carbon::parse($start);
        $last = Carbon::parse($end);

        $categories = [];
        $sales = [];
        $purchases = [];

        while ($cursor->lessThanOrEqualTo($last)) {
            $d = $cursor->format('Y-m-d');
            $categories[] = isset($dayLabels[$cursor->dayOfWeekIso]) ? $dayLabels[$cursor->dayOfWeekIso] : $cursor->format('d/m');
            $sales[] = $this->salesTotal($establishment_id, $d, $d);
            $purchases[] = $this->purchasesTotal($establishment_id, $d, $d);
            $cursor->addDay();
        }

        return compact('categories', 'sales', 'purchases');
    }

    /**
     * Segmentos semanales (bloques de 7 días) dentro del mes que contiene el
     * rango.
     */
    private function bucketsByWeek($establishment_id, $start, $end)
    {
        $month_start = Carbon::parse($start)->startOfMonth();
        $month_end = Carbon::parse($end)->endOfMonth();

        $cursor = $month_start->copy();
        $week = 1;

        $categories = [];
        $sales = [];
        $purchases = [];

        while ($cursor->lessThanOrEqualTo($month_end)) {
            $w_start = $cursor->copy();
            $w_end = $cursor->copy()->addDays(6);
            if ($w_end->greaterThan($month_end)) {
                $w_end = $month_end->copy();
            }

            $categories[] = 'Sem ' . $week;
            $sales[] = $this->salesTotal($establishment_id, $w_start->format('Y-m-d'), $w_end->format('Y-m-d'));
            $purchases[] = $this->purchasesTotal($establishment_id, $w_start->format('Y-m-d'), $w_end->format('Y-m-d'));

            $cursor = $w_end->copy()->addDay();
            $week++;
        }

        return compact('categories', 'sales', 'purchases');
    }

    /**
     * Un segmento por mes a lo largo del rango [inicio, fin] (usado en la vista
     * anual).
     */
    private function bucketsByMonth($establishment_id, $start, $end)
    {
        $cursor = Carbon::parse($start)->startOfMonth();
        $last = Carbon::parse($end)->startOfMonth();

        $categories = [];
        $sales = [];
        $purchases = [];

        while ($cursor->lessThanOrEqualTo($last)) {
            $m_start = $cursor->copy()->startOfMonth()->format('Y-m-d');
            $m_end = $cursor->copy()->endOfMonth()->format('Y-m-d');

            $categories[] = $this->monthLabels[$cursor->month];
            $sales[] = $this->salesTotal($establishment_id, $m_start, $m_end);
            $purchases[] = $this->purchasesTotal($establishment_id, $m_start, $m_end);

            $cursor->addMonthNoOverflow();
        }

        return compact('categories', 'sales', 'purchases');
    }

    /**
     * Ventas mes a mes del año actual vs el año anterior, desde enero hasta el
     * mes actual. Por defecto abarca todo el negocio.
     */
    public function salesGrowth($request)
    {
        $establishment_id = isset($request['establishment_id']) ? $request['establishment_id'] : null;

        [, $range_end] = $this->resolveRange($request);

        $now = Carbon::now();
        $end_date = Carbon::parse($range_end);
        $current_year = (int) $end_date->format('Y');
        $previous_year = $current_year - 1;

        // En el año en curso solo se llega hasta el mes actual; los años pasados son completos.
        $current_month = $current_year === (int) $now->format('Y')
            ? (int) $now->format('n')
            : 12;

        $categories = [];
        $current = [];
        $previous = [];

        for ($m = 1; $m <= $current_month; $m++) {
            $mm = str_pad($m, 2, '0', STR_PAD_LEFT);

            $cur_start = "{$current_year}-{$mm}-01";
            $cur_end = Carbon::parse($cur_start)->endOfMonth()->format('Y-m-d');

            $prev_start = "{$previous_year}-{$mm}-01";
            $prev_end = Carbon::parse($prev_start)->endOfMonth()->format('Y-m-d');

            $categories[] = $this->monthLabels[$m];
            $current[] = $this->salesTotal($establishment_id, $cur_start, $cur_end);
            $previous[] = $this->salesTotal($establishment_id, $prev_start, $prev_end);
        }

        return [
            'categories' => $categories,
            'current' => $current,
            'previous' => $previous,
            'current_label' => (string) $current_year,
            'previous_label' => (string) $previous_year,
        ];
    }

    /**
     * Variación porcentual de $current respecto a $previous.
     * Devuelve 100 cuando el mes anterior no hubo nada pero ahora sí,
     * y 0 cuando ambos son cero, para evitar la división por cero.
     */
    private function variation($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Ventas = (documentos de venta - notas de crédito) + notas de venta, con
     * USD convertido a PEN.
     * Pasa $hour (0-23) para limitar a una sola hora del día indicado.
     */
    private function salesTotal($establishment_id, $start, $end, $hour = null)
    {
        $sum_expr = "CASE WHEN currency_type_id = 'PEN' THEN total ELSE total * exchange_rate_sale END";

        $document_sales = (float) Document::query()
            ->whereIn('state_type_id', $this->acceptedStates)
            ->whereIn('document_type_id', $this->salesDocumentTypes)
            ->whereBetween('date_of_issue', [$start, $end])
            ->when($establishment_id, function ($q) use ($establishment_id) {
                $q->where('establishment_id', $establishment_id);
            })
            ->when(!is_null($hour), $this->hourFilter($hour))
            ->sum(\DB::raw($sum_expr));

        $document_credit_notes = (float) Document::query()
            ->whereIn('state_type_id', $this->acceptedStates)
            ->where('document_type_id', '07')
            ->whereBetween('date_of_issue', [$start, $end])
            ->when($establishment_id, function ($q) use ($establishment_id) {
                $q->where('establishment_id', $establishment_id);
            })
            ->when(!is_null($hour), $this->hourFilter($hour))
            ->sum(\DB::raw($sum_expr));

        $sale_notes = (float) SaleNote::query()
            ->where('changed', false)
            ->whereStateTypeAccepted()
            ->whereBetween('date_of_issue', [$start, $end])
            ->when($establishment_id, function ($q) use ($establishment_id) {
                $q->where('establishment_id', $establishment_id);
            })
            ->when(!is_null($hour), $this->hourFilter($hour))
            ->sum(\DB::raw($sum_expr));

        return round(($document_sales - $document_credit_notes) + $sale_notes, 2);
    }

    /**
     * Compras = total + percepción, con USD convertido a PEN.
     * Pasa $hour (0-23) para limitar a una sola hora del día indicado.
     */
    private function purchasesTotal($establishment_id, $start, $end, $hour = null)
    {
        $sum_expr = "CASE WHEN currency_type_id = 'PEN' THEN (total + COALESCE(total_perception, 0)) "
            . "ELSE (total + COALESCE(total_perception, 0)) * exchange_rate_sale END";

        $total = (float) Purchase::query()
            ->whereStateTypeAccepted()
            ->whereBetween('date_of_issue', [$start, $end])
            ->when($establishment_id, function ($q) use ($establishment_id) {
                $q->where('establishment_id', $establishment_id);
            })
            ->when(!is_null($hour), $this->hourFilter($hour))
            ->sum(\DB::raw($sum_expr));

        return round($total, 2);
    }

    /**
     * Devuelve un closure de consulta que restringe time_of_issue a la hora
     * indicada.
     */
    private function hourFilter($hour)
    {
        $hh = str_pad((int) $hour, 2, '0', STR_PAD_LEFT);

        return function ($q) use ($hh) {
            $q->whereBetween('time_of_issue', ["{$hh}:00:00", "{$hh}:59:59"]);
        };
    }

    private function formatNumber($number)
    {
        return number_format($number, 2, '.', '');
    }
}
