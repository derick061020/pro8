<?php

namespace Modules\Hotel\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/**
 * Relación de reservas (varias reservas con sus huéspedes) en Excel.
 */
class HotelReservationRelationExport implements FromView, ShouldAutoSize
{
    use Exportable;

    protected $data = [];

    public function data(array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function view(): View
    {
        return view('hotel::reservations.relation_excel', $this->data);
    }
}
