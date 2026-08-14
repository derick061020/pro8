<?php

namespace Modules\Hotel\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/**
 * Reporte de reservas del calendario (con filtros por día / rango).
 */
class HotelReservationExport implements FromView, ShouldAutoSize
{
    use Exportable;

    protected $records;
    protected $company;
    protected $establishment;
    protected $filters = [];
    protected $totals  = [];

    public function records($records)
    {
        $this->records = $records;

        return $this;
    }

    public function company($company)
    {
        $this->company = $company;

        return $this;
    }

    public function establishment($establishment)
    {
        $this->establishment = $establishment;

        return $this;
    }

    /** Resumen legible de los filtros aplicados (se imprime en la cabecera). */
    public function filters(array $filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /** Totales del rango (importe, pagado, deuda, noches). */
    public function totals(array $totals)
    {
        $this->totals = $totals;

        return $this;
    }

    public function view(): View
    {
        return view('hotel::reservations.report_excel', [
            'records'       => $this->records,
            'company'       => $this->company,
            'establishment' => $this->establishment,
            'filters'       => $this->filters,
            'totals'        => $this->totals,
        ]);
    }
}
