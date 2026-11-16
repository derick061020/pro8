@php
    $fmtDate = function ($date) {
        if (!$date) return '-';
        try {
            $months = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SET','OCT','NOV','DIC'];
            $c = \Carbon\Carbon::parse($date);
            return $c->format('d') . ' ' . $months[$c->month - 1] . ' ' . $c->format('Y');
        } catch (\Throwable $e) { return $date; }
    };
    $fmtTime = function ($time) { return $time ? substr((string) $time, 0, 5) : ''; };
    $money = function ($v) { return 'S/ ' . number_format((float) $v, 2); };
    $hotelName = optional($establishment)->description ?: (optional($company)->trade_name ?: optional($company)->name);
    $capacity = (int) ($rent->adults ?? 0) + (int) ($rent->children ?? 0);
    if ($capacity <= 0) $capacity = (int) ($rent->quantity_persons ?: 1);
    $amenities = ($room && is_array($room->amenities)) ? array_filter($room->amenities, 'is_string') : [];
    $roomText = $room ? ($room->short_description ?: $room->description) : null;
    $createdAt = $rent->created_at ? \Carbon\Carbon::parse($rent->created_at) : null;
@endphp
<html>
<head>
<style>
    body { font-family: sans-serif; font-size: 10pt; color: #222; }
    .muted { color: #777; }
    .small { font-size: 8.5pt; }
    .head td { vertical-align: top; }
    .hotel-name { font-size: 13pt; font-weight: bold; }
    .label { color: #555; }
    .code-title { font-size: 9pt; font-weight: bold; text-align: center; }
    .code { font-size: 18pt; font-weight: bold; text-align: center; letter-spacing: 1px; }
    .meta { text-align: center; font-size: 8.5pt; color: #666; line-height: 1.6; }
    .dates td { padding: 2px 0; }
    .date-title { font-size: 11pt; font-weight: bold; }
    .date-value { font-size: 14pt; font-weight: bold; color: #b91c1c; }
    .totals { text-align: right; font-size: 11pt; font-weight: bold; line-height: 1.5; }
    .debt { color: #b91c1c; }
    .section { font-size: 11pt; font-weight: bold; margin: 14px 0 6px; color: #111; }
    .box { border-top: 1px solid #ddd; margin-top: 10px; padding-top: 8px; }
    .policy { font-size: 8.5pt; line-height: 1.45; margin-bottom: 6px; }
    .policy-red { color: #b91c1c; font-weight: bold; }
    .sign td { text-align: center; font-size: 9pt; font-weight: bold; padding-top: 40px; }
</style>
</head>
<body>

<table class="head" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td width="66%">
            @if($logoPath)
                <img src="{{ $logoPath }}" style="max-height: 55px; max-width: 200px;"><br>
            @endif
            <span class="hotel-name">{{ $hotelName }}</span><br>
            <span class="small muted">
                @if(optional($company)->number) RUC {{ $company->number }} · @endif
                {{ optional($establishment)->address }}
                @if(optional($establishment)->telephone) · Tel. {{ $establishment->telephone }} @endif
                @if(optional($establishment)->email) · {{ $establishment->email }} @endif
            </span>
            <br><br>
            Estimado(a) huésped,<br><br>
            A continuación la constancia de su reserva de alojamiento.<br><br>
            <span class="label">Titular:</span> <b>{{ $customer->name ?? '-' }}</b><br>
            <span class="label">DNI / RUC:</span> <b>{{ $customer->number ?? '-' }}</b><br>
            @if(!empty($customer->telephone))<span class="label">Teléfono:</span> <b>{{ $customer->telephone }}</b><br>@endif
            <span class="label">Habitación:</span> <b>{{ $room ? $room->name : '-' }}@if($room && $room->category) - {{ $room->category->description }}@endif</b><br>
            <span class="label">Estadía:</span> <b>{{ $durationText }}</b><br>
            <span class="label">Personas:</span> <b>{{ $capacity }}</b>
            @if($rent->adults || $rent->children)
                <span class="small muted">({{ (int) $rent->adults }} adulto(s){{ $rent->children ? ', ' . (int) $rent->children . ' niño(s)' : '' }})</span>
            @endif
        </td>
        <td width="34%" align="center">
            <div class="code-title">Código de reserva</div>
            <div class="code">{{ $code }}</div>
            <barcode code="{{ $qrText }}" type="QR" size="1.1" error="M" disableborder="1" />
            <div class="meta">
                @if($createdAt)
                    Fecha de reserva<br><b>{{ $fmtDate($createdAt) }}</b><br>
                    Hora de reserva<br><b>{{ $createdAt->format('h:i a') }}</b><br>
                @endif
                @if($rent->reservation_origin)
                    Medio<br><b>{{ ucfirst($rent->reservation_origin) }}</b>
                @endif
            </div>
        </td>
    </tr>
</table>

<table class="dates" width="100%" cellspacing="0" cellpadding="0" style="margin-top: 10px;">
    <tr>
        <td width="33%"><div class="date-title">Check-in</div></td>
        <td width="33%"><div class="date-title">Check-out</div></td>
        <td width="34%" rowspan="2" class="totals">
            Total: {{ $money($total) }}<br>
            Pagado: {{ $money($paid) }}<br>
            <span class="{{ $debt > 0.009 ? 'debt' : '' }}">Saldo: {{ $money(max(0, $debt)) }}</span>
        </td>
    </tr>
    <tr>
        <td><div class="date-value">{{ $fmtDate($rent->input_date) }}<br>{{ $fmtTime($rent->input_time) }}</div></td>
        <td><div class="date-value">{{ $fmtDate($rent->output_date) }}<br>{{ $fmtTime($rent->output_time) }}</div></td>
    </tr>
</table>

@if($companions->count())
    <div class="box">
        <div class="section">Acompañantes</div>
        @foreach($companions as $p)
            <div class="small">• <b>{{ $p->name ?? 'Sin nombre' }}</b>@if(!empty($p->number)) — {{ $p->number }}@endif @if(!empty($p->type)) ({{ $p->type === 'child' ? 'niño' : 'adulto' }})@endif</div>
        @endforeach
    </div>
@endif

@if($room && ($roomImagePath || $roomText || $amenities || $room->capacity || $room->beds))
    <div class="box">
        <div class="section">Detalle de la habitación</div>
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                @if($roomImagePath)
                    <td width="32%" style="vertical-align: top;">
                        <img src="{{ $roomImagePath }}" style="width: 160px;">
                    </td>
                @endif
                <td style="vertical-align: top; font-size: 9pt; line-height: 1.5;">
                    <b>{{ $room->name }}@if($room->category) — {{ $room->category->description }}@endif</b><br>
                    @if($room->capacity)<b>Capacidad:</b> {{ $room->capacity }} persona(s)<br>@endif
                    @if($room->beds)<b>Camas:</b> {{ $room->beds }}<br>@endif
                    @if($room->size)<b>Tamaño:</b> {{ $room->size }}<br>@endif
                    @if($roomText){{ strip_tags($roomText) }}<br>@endif
                    @if($amenities)<b>Incluye:</b> {{ implode(', ', $amenities) }}@endif
                </td>
            </tr>
        </table>
    </div>
@endif

@if($rent->notes)
    <div class="box">
        <div class="section">Observaciones</div>
        <div class="small">{{ strip_tags($rent->notes) }}</div>
    </div>
@endif

<div class="box">
    <div class="section">Entrega de la habitación</div>
    <div class="policy">Presente esta constancia (impresa o en el celular) y el DNI del titular al momento del ingreso. La habitación se entrega al titular de la reserva o a la persona que él indique.</div>
    <div class="section">Check In-Out</div>
    <div class="policy policy-red">El ingreso es a partir de las {{ $fmtTime($rent->input_time) ?: '—' }} y la salida hasta las {{ $fmtTime($rent->output_time) ?: '—' }} del día indicado. Si necesita quedarse más tiempo, coordínelo con recepción.</div>
    <div class="section">Cambios y anulaciones</div>
    <div class="policy">Para modificar o anular su reserva comuníquese con el hotel@if(optional($establishment)->telephone) al {{ $establishment->telephone }}@endif @if(optional($establishment)->email) o al correo {{ $establishment->email }}@endif.</div>
</div>

<table class="sign" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td width="50%">______________________________<br>Recepción {{ $hotelName }}</td>
        <td width="50%">______________________________<br>{{ $customer->name ?? 'Titular' }}</td>
    </tr>
</table>

</body>
</html>
