<?php

namespace Modules\Hotel\Http\Controllers;

use App\Models\Tenant\Company;
use App\Models\Tenant\Establishment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Hotel\Exports\HotelReservationRelationExport;
use Modules\Hotel\Models\HotelRent;

/**
 * "Relación de reservas": el usuario marca varias reservas (p. ej. un grupo de
 * 10 habitaciones) y saca una sola lista en PDF/Excel para enviársela al
 * cliente, con el titular y los acompañantes de cada habitación.
 * Pedido de Junior (2026-11-23).
 */
class HotelReservationRelationController extends Controller
{
    private const DOC_LABELS = ['1' => 'DNI', '6' => 'RUC', '4' => 'Carnet ext.', '7' => 'Pasaporte'];

    public function index()
    {
        $establishment = Establishment::find(auth()->user()->establishment_id);

        return view('hotel::reservations.relation', compact('establishment'));
    }

    /**
     * Reservas para elegir: ingreso dentro del rango, de la sucursal actual y
     * sin finalizar. `search` filtra por titular, documento o habitación.
     */
    public function records(Request $request)
    {
        $from = $request->get('from') ?: now()->format('Y-m-d');
        $to   = $request->get('to') ?: now()->addDays(30)->format('Y-m-d');
        $search = trim((string) $request->get('search', ''));

        $rents = HotelRent::with(['room.category'])
            ->where('establishment_id', auth()->user()->establishment_id)
            ->where('status', '!=', 'FINALIZADO')
            ->whereBetween('input_date', [$from, $to])
            ->orderBy('input_date')
            ->orderBy('input_time')
            ->orderBy('id')
            ->get();

        $rows = $rents->map(function (HotelRent $rent) {
            $customer = $rent->customer ?: (object) [];

            return [
                'id'          => $rent->id,
                'code'        => $this->code($rent),
                'customer'    => $customer->name ?? '-',
                'number'      => $customer->number ?? '',
                'room'        => optional($rent->room)->name,
                'category'    => optional(optional($rent->room)->category)->description,
                'input_date'  => $rent->input_date,
                'input_time'  => substr((string) $rent->input_time, 0, 5),
                'output_date' => $rent->output_date,
                'output_time' => substr((string) $rent->output_time, 0, 5),
                'nights'      => (int) ($rent->duration ?: 1),
                'guests'      => count($this->guests($rent)),
                'is_reserve'  => (bool) $rent->is_reserve,
            ];
        });

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $rows = $rows->filter(function ($r) use ($needle) {
                return str_contains(mb_strtolower($r['customer'] . ' ' . $r['number'] . ' ' . $r['room'] . ' ' . $r['code']), $needle);
            });
        }

        return response()->json(['records' => $rows->values()]);
    }

    /**
     * Descarga de las reservas marcadas: PDF (por defecto) o Excel (`format=xlsx`).
     */
    public function download(Request $request)
    {
        $ids = collect(explode(',', (string) $request->get('ids')))
            ->map(function ($id) { return (int) $id; })
            ->filter()
            ->unique()
            ->values();

        abort_if($ids->isEmpty(), 422, 'Seleccione al menos una reserva.');

        $rents = HotelRent::with(['room.category'])
            ->whereIn('id', $ids)
            ->where('establishment_id', auth()->user()->establishment_id)
            ->orderBy('input_date')
            ->orderBy('id')
            ->get();

        abort_if($rents->isEmpty(), 404, 'No se encontraron las reservas.');

        $data = $this->buildData($rents, (string) $request->get('title', ''));

        if ($request->get('format') === 'xlsx') {
            return (new HotelReservationRelationExport)
                ->data($data)
                ->download($data['basename'] . '.xlsx');
        }

        $html = view('hotel::reservations.relation_pdf', $data)->render();

        $pdf = new \Mpdf\Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4-L',
            'default_font'  => 'dejavusanscondensed',
            'margin_top'    => 10,
            'margin_bottom' => 32,
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_footer' => 4,
        ]);
        $pdf->SetTitle('Relación de reservas');
        $pdf->SetAuthor(optional($data['company'])->name ?: 'Hotel');
        $pdf->WriteHTML($html);

        return response(
            $pdf->Output($data['basename'] . '.pdf', \Mpdf\Output\Destination::STRING_RETURN),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $data['basename'] . '.pdf"',
            ]
        );
    }

    /**
     * Datos comunes del PDF y del Excel: cabecera del hotel y una fila por
     * huésped (titular + acompañantes), agrupadas por reserva.
     */
    private function buildData($rents, string $title): array
    {
        $establishment = Establishment::with(['country', 'department', 'province', 'district'])
            ->find(auth()->user()->establishment_id);
        $company = Company::first();

        $logoPath = (!empty($company->logo) && file_exists(public_path('storage/uploads/logos/' . $company->logo)))
            ? public_path('storage/uploads/logos/' . $company->logo)
            : null;

        $groups = [];
        $amenities = [];
        $guestsCount = 0;
        foreach ($rents as $index => $rent) {
            $guests = $this->guests($rent);
            $guestsCount += count($guests);
            $groups[] = [
                'number'      => $index + 1,
                'code'        => $this->code($rent),
                'room'        => optional($rent->room)->name,
                'category'    => optional(optional($rent->room)->category)->description,
                'input_date'  => $this->fmtDate($rent->input_date),
                'input_time'  => $this->fmtTime($rent->input_time),
                'output_date' => $this->fmtDate($rent->output_date),
                'output_time' => $this->fmtTime($rent->output_time),
                'stay'        => $this->stayText($rent),
                'guests'      => $guests,
            ];
            if ($rent->room && is_array($rent->room->amenities)) {
                foreach ($rent->room->amenities as $a) {
                    if (is_string($a) && $a !== '') $amenities[mb_strtolower($a)] = $a;
                }
            }
        }

        $location = $establishment ? array_filter([
            optional($establishment->district)->description,
            optional($establishment->province)->description,
            optional($establishment->department)->description,
        ]) : [];

        $first = $rents->first();
        $firstCustomer = $first && $first->customer ? $first->customer : null;

        return [
            'title'         => $title !== '' ? $title : 'RELACIÓN DE RESERVAS',
            'company'       => $company,
            'establishment' => $establishment,
            'hotelName'     => optional($establishment)->description ?: (optional($company)->trade_name ?: optional($company)->name),
            'logoPath'      => $logoPath,
            'location'      => implode(' - ', $location),
            'groups'        => $groups,
            'roomsCount'    => count($groups),
            'guestsCount'   => $guestsCount,
            'amenities'     => array_values($amenities),
            'reservedBy'    => $firstCustomer->name ?? '',
            'generatedAt'   => now()->format('d/m/Y H:i'),
            'basename'      => 'Relacion-reservas-' . now()->format('Ymd-His'),
        ];
    }

    /** Titular + acompañantes (sin repetir al titular). */
    private function guests(HotelRent $rent): array
    {
        $customer = $rent->customer ?: (object) [];
        $mainNumber = trim((string) ($customer->number ?? ''));

        $list = [[
            'name'     => $customer->name ?? '-',
            'doc'      => $this->docLabel($customer->identity_document_type_id ?? null, $mainNumber),
            'number'   => $mainNumber,
            'is_main'  => true,
        ]];

        foreach ((array) ($rent->data_persons ?: []) as $p) {
            $p = (array) $p;
            $name = trim((string) ($p['name'] ?? ''));
            $number = trim((string) ($p['number'] ?? ''));
            if ($name === '' && $number === '') continue;
            // Registrados al revés (el nombre en la columna del documento):
            // "568012449" como nombre y "Rafael Perez" como documento.
            if ($this->looksLikeDocNumber($name) && $number !== '' && !preg_match('/\d/', $number)) {
                [$name, $number] = [$number, $name];
            }
            if ($number !== '' && $number === $mainNumber) continue;
            $list[] = [
                'name'    => $name !== '' ? $name : 'Sin nombre',
                'doc'     => $this->docLabel($p['identity_document_type_id'] ?? null, $number),
                'number'  => $number,
                'is_main' => false,
            ];
        }

        return $list;
    }

    private function looksLikeDocNumber(string $s): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9-]{5,15}$/', $s) && preg_match('/\d/', $s);
    }

    private function docLabel($typeId, string $number): string
    {
        $typeId = $typeId !== null ? (string) $typeId : '';
        if (isset(self::DOC_LABELS[$typeId])) return self::DOC_LABELS[$typeId];
        if ($number === '') return '';
        if (ctype_digit($number) && strlen($number) === 8) return 'DNI';
        if (ctype_digit($number) && strlen($number) === 11) return 'RUC';

        return 'Doc.';
    }

    private function code(HotelRent $rent): string
    {
        return 'R-' . str_pad((string) $rent->id, 6, '0', STR_PAD_LEFT);
    }

    private function stayText(HotelRent $rent): string
    {
        $n = (int) ($rent->duration ?: 1);
        switch ($rent->rental_period_type) {
            case 'hour':  return $n . ' ' . ($n === 1 ? 'hora' : 'horas');
            case 'month': return $n . ' ' . ($n === 1 ? 'mes' : 'meses');
            default:      return $n . ' ' . ($n === 1 ? 'noche' : 'noches');
        }
    }

    private function fmtDate($date): string
    {
        if (!$date) return '-';
        try {
            return Carbon::parse($date)->format('d/m/Y');
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }

    private function fmtTime($time): string
    {
        if (!$time) return '';
        try {
            return Carbon::parse($time)->format('h:i a');
        } catch (\Throwable $e) {
            return substr((string) $time, 0, 5);
        }
    }
}
