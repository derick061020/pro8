{{--
    Reporte de reservas en PDF (A4 apaisado).

    Pensado para imprimirse y dejarse en recepción: arriba el resumen de cuánta
    gente entra, en medio el desglose por día y por tipo de habitación, y abajo
    el detalle reserva por reserva.

    Ojo al tocar los estilos: mPDF no soporta flexbox ni grid, así que la
    maquetación va con tablas y anchos en porcentaje.
--}}
@php
    $money = function ($value) {
        return 'S/ ' . number_format((float) $value, 2);
    };

    $shortDate = function ($value) {
        if (!$value) {
            return '';
        }
        try {
            return \Carbon\Carbon::parse($value)->format('d/m/Y');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    };

    $weekDay = function ($value) {
        $days = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        try {
            return $days[(int) \Carbon\Carbon::parse($value)->dayOfWeek] ?? '';
        } catch (\Throwable $e) {
            return '';
        }
    };

    $hour = function ($value) {
        return $value ? substr((string) $value, 0, 5) : '';
    };

    $periodLabel = $start->format('d/m/Y') === $end->format('d/m/Y')
        ? $start->format('d/m/Y')
        : $start->format('d/m/Y') . ' — ' . $end->format('d/m/Y');

    // Los filtros de periodo y criterio ya salen en la cabecera; aquí sólo se
    // muestran los que de verdad acotan el listado.
    $extraFilters = collect($filters)->except(['Periodo', 'Criterio', 'Reservas'])->filter(function ($v) {
        return $v !== null && $v !== '';
    });

    $paymentClass = [
        'Pagado'             => 'pill-ok',
        'Adelanto / parcial' => 'pill-warn',
        'Sin pagar'          => 'pill-bad',
    ];

    $statusClass = [
        'Reserva'    => 'pill-blue',
        'En curso'   => 'pill-teal',
        'Finalizado' => 'pill-slate',
    ];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de reservas</title>
    <style>
        @page {
            header: html_head;
            footer: html_foot;
        }

        body {
            font-family: dejavusanscondensed, sans-serif;
            font-size: 7.6pt;
            color: #0f172a;
            line-height: 1.35;
        }

        /* ---------- Cabecera y pie que se repiten en cada página ---------- */
        .head-table { width: 100%; border-collapse: collapse; }
        .head-brand { font-size: 12.5pt; font-weight: bold; color: #0f172a; letter-spacing: -0.2pt; }
        .head-sub   { font-size: 7pt; color: #64748b; padding-top: 1.5mm; }
        .head-right { text-align: right; }
        .head-kicker {
            font-size: 6.6pt; letter-spacing: 1.6pt; color: #0d9488;
            font-weight: bold; text-transform: uppercase;
        }
        .head-title { font-size: 13pt; font-weight: bold; color: #0f172a; letter-spacing: -0.3pt; }
        .head-period { font-size: 7.4pt; color: #334155; padding-top: 0.8mm; }
        .head-rule { border-bottom: 0.9pt solid #0d9488; margin-top: 2.2mm; }
        .head-rule-soft { border-bottom: 0.3pt solid #cbd5e1; margin-top: 0.7mm; }

        .foot-table { width: 100%; border-collapse: collapse; font-size: 6.6pt; color: #94a3b8; }
        .foot-table td { border-top: 0.3pt solid #e2e8f0; padding-top: 1.4mm; }
        .foot-right { text-align: right; }

        /* ---------- Tarjetas de resumen ---------- */
        .kpi { width: 100%; border-collapse: separate; border-spacing: 1.6mm 0; margin-top: 1mm; }
        .kpi td {
            width: 14.28%;
            background-color: #f8fafc;
            border: 0.4pt solid #e2e8f0;
            border-top: 1.6pt solid #0d9488;
            padding: 2.2mm 2.4mm;
        }
        .kpi td.kpi-money { border-top-color: #334155; }
        .kpi td.kpi-debt  { border-top-color: #dc2626; }
        .kpi-label { font-size: 6.2pt; letter-spacing: 0.9pt; color: #64748b; text-transform: uppercase; }
        .kpi-value { font-size: 13pt; font-weight: bold; color: #0f172a; padding-top: 0.8mm; letter-spacing: -0.4pt; }
        .kpi-value-sm { font-size: 10.5pt; }
        .kpi-hint { font-size: 6.2pt; color: #94a3b8; padding-top: 0.4mm; }
        .kpi-debt .kpi-value { color: #dc2626; }

        /* ---------- Chips de filtros ---------- */
        .filters { margin-top: 3mm; font-size: 6.9pt; color: #475569; }
        .chip {
            background-color: #f1f5f9; border: 0.3pt solid #e2e8f0;
            padding: 0.9mm 1.8mm; color: #334155;
        }
        .chip b { color: #0f172a; }

        /* ---------- Secciones ---------- */
        .section-title {
            font-size: 8.4pt; font-weight: bold; color: #0f172a;
            text-transform: uppercase; letter-spacing: 0.9pt;
            border-left: 1.8pt solid #0d9488; padding-left: 2mm;
            margin-bottom: 1.6mm;
        }
        .section-note { font-size: 6.6pt; color: #94a3b8; font-weight: normal; letter-spacing: 0; text-transform: none; }

        /* ---------- Tablas de datos ---------- */
        table.data { width: 100%; border-collapse: collapse; }
        table.data thead th {
            background-color: #0f172a; color: #ffffff;
            font-size: 6.4pt; font-weight: bold;
            letter-spacing: 0.5pt; text-transform: uppercase;
            padding: 1.8mm 1.4mm; text-align: left;
        }
        table.data tbody td {
            border-bottom: 0.3pt solid #e2e8f0;
            padding: 1.5mm 1.4mm;
            vertical-align: top;
        }
        table.data tbody tr.zebra td { background-color: #f8fafc; }
        table.data tfoot td {
            background-color: #0f172a; color: #ffffff;
            font-weight: bold; padding: 1.8mm 1.4mm;
            font-size: 7.4pt;
        }

        .num { text-align: right; }
        .ctr { text-align: center; }
        .muted { color: #94a3b8; font-size: 6.4pt; }
        .strong { font-weight: bold; }
        .debt { color: #dc2626; font-weight: bold; }
        .paid-ok { color: #059669; }
        .idx { color: #cbd5e1; font-size: 6.6pt; }

        /* ---------- Píldoras de estado ---------- */
        .pill {
            font-size: 6.1pt; font-weight: bold; padding: 0.7mm 1.5mm;
            text-transform: uppercase; letter-spacing: 0.3pt;
        }
        .pill-ok    { background-color: #ecfdf5; color: #047857; border: 0.3pt solid #a7f3d0; }
        .pill-warn  { background-color: #fffbeb; color: #b45309; border: 0.3pt solid #fde68a; }
        .pill-bad   { background-color: #fef2f2; color: #b91c1c; border: 0.3pt solid #fecaca; }
        .pill-blue  { background-color: #eff6ff; color: #1d4ed8; border: 0.3pt solid #bfdbfe; }
        .pill-teal  { background-color: #f0fdfa; color: #0f766e; border: 0.3pt solid #99f6e4; }
        .pill-slate { background-color: #f1f5f9; color: #475569; border: 0.3pt solid #cbd5e1; }

        /* ---------- Resúmenes lado a lado ---------- */
        .split { width: 100%; border-collapse: separate; border-spacing: 4mm 0; }
        .split > tr > td { vertical-align: top; }
        table.mini { width: 100%; border-collapse: collapse; }
        table.mini thead th {
            font-size: 6.2pt; text-transform: uppercase; letter-spacing: 0.5pt;
            color: #64748b; border-bottom: 0.7pt solid #cbd5e1;
            padding: 1.2mm 1.2mm; text-align: left;
        }
        table.mini tbody td { padding: 1.3mm 1.2mm; border-bottom: 0.3pt solid #eef2f6; }
        table.mini tbody tr.peak td { background-color: #f0fdfa; }
        table.mini tfoot td {
            border-top: 0.7pt solid #cbd5e1; padding: 1.3mm 1.2mm;
            font-weight: bold; font-size: 7pt;
        }
        .bar { background-color: #0d9488; height: 1.5mm; }
        .bar-track { background-color: #e2e8f0; height: 1.5mm; }

        .empty {
            border: 0.4pt dashed #cbd5e1; background-color: #f8fafc;
            padding: 12mm; text-align: center; color: #94a3b8; font-size: 8pt;
        }
    </style>
</head>
<body>

{{-- ======================= CABECERA REPETIDA ======================= --}}
<htmlpageheader name="head">
    <table class="head-table">
        <tr>
            <td>
                <div class="head-brand">{{ $company->name ?? 'Hotel' }}</div>
                <div class="head-sub">
                    @if(!empty($company->number))RUC {{ $company->number }} &nbsp;·&nbsp; @endif
                    {{ $establishment->description ?? '' }}@if(!empty($establishment->address)) &nbsp;·&nbsp; {{ $establishment->address }}@endif
                </div>
            </td>
            <td class="head-right">
                <div class="head-kicker">Reporte de reservas</div>
                <div class="head-title">{{ $periodLabel }}</div>
                <div class="head-period">{{ $criterion }}</div>
            </td>
        </tr>
    </table>
    <div class="head-rule"></div>
    <div class="head-rule-soft"></div>
</htmlpageheader>

{{-- ========================= PIE REPETIDO ========================= --}}
<htmlpagefooter name="foot">
    <table class="foot-table">
        <tr>
            <td>
                Generado el {{ date('d/m/Y') }} a las {{ date('H:i') }}
                @if(auth()->check()) · {{ auth()->user()->name }} @endif
            </td>
            <td class="foot-right">Página {PAGENO} de {nbpg}</td>
        </tr>
    </table>
</htmlpagefooter>

{{-- ===================== TARJETAS DE RESUMEN ===================== --}}
<table class="kpi">
    <tr>
        <td>
            <div class="kpi-label">Reservas</div>
            <div class="kpi-value">{{ $totals['count'] ?? 0 }}</div>
            <div class="kpi-hint">registros en el periodo</div>
        </td>
        <td>
            <div class="kpi-label">Huéspedes</div>
            <div class="kpi-value">{{ $totals['guests'] ?? 0 }}</div>
            <div class="kpi-hint">{{ $totals['adults'] ?? 0 }} adultos · {{ $totals['children'] ?? 0 }} niños</div>
        </td>
        <td>
            <div class="kpi-label">Habitaciones</div>
            <div class="kpi-value">{{ $totals['rooms'] ?? 0 }}</div>
            <div class="kpi-hint">distintas ocupadas</div>
        </td>
        <td>
            <div class="kpi-label">Noches</div>
            <div class="kpi-value">{{ $totals['nights'] ?? 0 }}</div>
            <div class="kpi-hint">suma de estadías</div>
        </td>
        <td class="kpi-money">
            <div class="kpi-label">Importe</div>
            <div class="kpi-value kpi-value-sm">{{ $money($totals['total'] ?? 0) }}</div>
            <div class="kpi-hint">total facturable</div>
        </td>
        <td class="kpi-money">
            <div class="kpi-label">Pagado</div>
            <div class="kpi-value kpi-value-sm paid-ok">{{ $money($totals['paid'] ?? 0) }}</div>
            <div class="kpi-hint">
                @php $pct = ($totals['total'] ?? 0) > 0 ? round(100 * ($totals['paid'] ?? 0) / $totals['total']) : 0; @endphp
                {{ $pct }}% cobrado
            </div>
        </td>
        <td class="kpi-debt">
            <div class="kpi-label">Por cobrar</div>
            <div class="kpi-value kpi-value-sm">{{ $money($totals['debt'] ?? 0) }}</div>
            <div class="kpi-hint">saldo pendiente</div>
        </td>
    </tr>
</table>

@if($extraFilters->count())
    <div class="filters">
        @foreach($extraFilters as $label => $value)
            <span class="chip"><b>{{ $label }}:</b> {{ $value }}</span>&nbsp;
        @endforeach
    </div>
@endif

@if(count($records) === 0)

    <div style="margin-top: 8mm;">
        <div class="empty">
            No se encontraron reservas con los filtros seleccionados.
        </div>
    </div>

@else

    {{-- ============ RESÚMENES: POR DÍA Y POR TIPO DE HABITACIÓN ============ --}}
    @php
        $maxDayGuests = max(1, (int) $byDay->max('guests'));
        $peakGuests   = (int) $byDay->max('guests');
    @endphp

    <table class="split" style="margin-top: 4.5mm;">
        <tr>
            <td width="58%">
                <div class="section-title">
                    Ingresos por día
                    <span class="section-note">— cuánta gente entra cada fecha</span>
                </div>
                <table class="mini">
                    <thead>
                        <tr>
                            <th width="21%">Fecha</th>
                            <th width="11%" class="ctr">Res.</th>
                            <th width="11%" class="ctr">Hab.</th>
                            <th width="13%" class="ctr">Huésp.</th>
                            <th width="20%">Ocupación</th>
                            <th width="24%" class="num">Importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byDay as $day)
                            <tr class="{{ $day['guests'] === $peakGuests && $peakGuests > 0 ? 'peak' : '' }}">
                                <td>
                                    <span class="strong">{{ $shortDate($day['date']) }}</span>
                                    <span class="muted">{{ $weekDay($day['date']) }}</span>
                                </td>
                                <td class="ctr">{{ $day['count'] }}</td>
                                <td class="ctr">{{ $day['rooms'] }}</td>
                                <td class="ctr strong">{{ $day['guests'] }}</td>
                                <td>
                                    @php $w = max(2, round(100 * $day['guests'] / $maxDayGuests)); @endphp
                                    <table class="bar-track" width="100%" cellpadding="0" cellspacing="0">
                                        <tr><td class="bar" width="{{ $w }}%"></td><td width="{{ 100 - $w }}%"></td></tr>
                                    </table>
                                </td>
                                <td class="num">{{ $money($day['total']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td class="ctr">{{ $totals['count'] ?? 0 }}</td>
                            <td class="ctr">{{ $totals['rooms'] ?? 0 }}</td>
                            <td class="ctr">{{ $totals['guests'] ?? 0 }}</td>
                            <td></td>
                            <td class="num">{{ $money($totals['total'] ?? 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </td>
            <td width="42%">
                <div class="section-title">
                    Por tipo de habitación
                    <span class="section-note">— reparto del periodo</span>
                </div>
                <table class="mini">
                    <thead>
                        <tr>
                            <th width="40%">Tipo</th>
                            <th width="14%" class="ctr">Res.</th>
                            <th width="16%" class="ctr">Huésp.</th>
                            <th width="30%" class="num">Importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byCategory as $cat)
                            <tr>
                                <td class="strong">{{ $cat['category'] }}</td>
                                <td class="ctr">{{ $cat['count'] }}</td>
                                <td class="ctr">{{ $cat['guests'] }}</td>
                                <td class="num">{{ $money($cat['total']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td class="ctr">{{ $totals['count'] ?? 0 }}</td>
                            <td class="ctr">{{ $totals['guests'] ?? 0 }}</td>
                            <td class="num">{{ $money($totals['total'] ?? 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===================== DETALLE DE RESERVAS ===================== --}}
    <div class="section-title" style="margin-top: 5.5mm;">
        Detalle de reservas
        <span class="section-note">— {{ count($records) }} {{ count($records) === 1 ? 'registro' : 'registros' }}</span>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="3%" class="ctr">#</th>
                <th width="7%">Hab.</th>
                <th width="19%">Huésped</th>
                <th width="8%">Teléfono</th>
                <th width="5%" class="ctr">Pax</th>
                <th width="9%">Ingreso</th>
                <th width="9%">Salida</th>
                <th width="4%" class="ctr">Nt</th>
                <th width="8%">Medio</th>
                <th width="9%">Estado</th>
                <th width="6.5%" class="num">Total</th>
                <th width="6.5%" class="num">Pagado</th>
                <th width="6%" class="num">Deuda</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $i => $row)
                <tr class="{{ $i % 2 === 1 ? 'zebra' : '' }}">
                    <td class="ctr idx">{{ $i + 1 }}</td>
                    <td>
                        <span class="strong">{{ $row['room'] }}</span>
                        @if(!empty($row['category']))
                            <div class="muted">{{ $row['category'] }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="strong">{{ $row['customer'] ?: '—' }}</span>
                        @if(!empty($row['customer_number']))
                            <div class="muted">Doc. {{ $row['customer_number'] }}</div>
                        @endif
                    </td>
                    <td>{{ $row['customer_telephone'] ?: '—' }}</td>
                    <td class="ctr">
                        {{ (int) $row['adults'] + (int) $row['children'] }}
                        @if((int) $row['children'] > 0)
                            <div class="muted">{{ (int) $row['adults'] }}A·{{ (int) $row['children'] }}N</div>
                        @endif
                    </td>
                    <td>
                        <span class="strong">{{ $shortDate($row['input_date']) }}</span>
                        <div class="muted">{{ $weekDay($row['input_date']) }} {{ $hour($row['input_time']) }}</div>
                    </td>
                    <td>
                        {{ $shortDate($row['output_date']) }}
                        <div class="muted">{{ $weekDay($row['output_date']) }} {{ $hour($row['output_time']) }}</div>
                    </td>
                    <td class="ctr">{{ $row['duration'] }}</td>
                    <td>
                        {{ $row['origin'] ?: '—' }}
                        <div class="muted">
                            <span class="pill {{ $statusClass[$row['status']] ?? 'pill-slate' }}">{{ $row['status'] }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="pill {{ $paymentClass[$row['payment_state']] ?? 'pill-slate' }}">{{ $row['payment_state'] }}</span>
                        @if(!empty($row['document_number']))
                            <div class="muted">{{ $row['document_number'] }}</div>
                        @endif
                    </td>
                    <td class="num strong">{{ number_format($row['total'], 2) }}</td>
                    <td class="num paid-ok">{{ number_format($row['paid'], 2) }}</td>
                    <td class="num {{ $row['debt'] > 0 ? 'debt' : 'muted' }}">{{ number_format($row['debt'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">TOTALES</td>
                <td class="ctr">{{ $totals['guests'] ?? 0 }}</td>
                <td colspan="2"></td>
                <td class="ctr">{{ $totals['nights'] ?? 0 }}</td>
                <td colspan="2"></td>
                <td class="num">{{ number_format($totals['total'] ?? 0, 2) }}</td>
                <td class="num">{{ number_format($totals['paid'] ?? 0, 2) }}</td>
                <td class="num">{{ number_format($totals['debt'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>

@endif

</body>
</html>
