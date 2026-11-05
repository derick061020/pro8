<?php

namespace Modules\Hotel\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/** Excel de Hotel → Ingresos diarios (mismos datos que la pantalla) */
class HotelIncomeExport implements FromView, ShouldAutoSize
{
    use Exportable;

    protected $report;

    public function report(array $report)
    {
        $this->report = $report;

        return $this;
    }

    public function view(): View
    {
        return view('hotel::reports.daily_income_excel', ['report' => $this->report]);
    }
}
