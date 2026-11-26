<html>
<head>
<style>
    body { font-family: dejavusanscondensed, sans-serif; font-size: 9pt; color: #222; }
    .head td { vertical-align: middle; }
    .hotel-name { font-size: 13pt; font-weight: bold; }
    .hotel-data { font-size: 8.5pt; line-height: 1.45; }
    .title { background: #2e7d32; color: #fff; font-size: 12pt; font-weight: bold; text-align: center; padding: 5px; }
    table.list { border-collapse: collapse; width: 100%; margin-top: 4px; }
    table.list th { background: #2e7d32; color: #fff; font-size: 8.5pt; padding: 5px 4px; border: 1px solid #1b5e20; text-align: center; }
    table.list td { border: 1px solid #9ccc9c; padding: 4px; font-size: 8.5pt; vertical-align: middle; }
    tr.odd td { background: #e8f5e9; }
    td.c { text-align: center; }
    td.n { text-align: center; font-weight: bold; }
    .main { font-weight: bold; }
    .muted { color: #666; }
    .summary { margin-top: 8px; font-size: 8.5pt; }
    .services { margin-top: 10px; font-size: 9pt; color: #2e7d32; font-weight: bold; }
</style>
</head>
<body>

<htmlpagefooter name="relfooter">
    <div style="font-size: 7.5pt; color: #888; text-align: right;">{{ $hotelName }} · Generado {{ $generatedAt }} · Página {PAGENO} de {nbpg}</div>
</htmlpagefooter>
<sethtmlpagefooter name="relfooter" value="on" />

<table class="head" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        @if($logoPath)
            <td width="22%"><img src="{{ $logoPath }}" style="max-height: 70px; max-width: 200px;"></td>
        @endif
        <td class="hotel-data">
            <span class="hotel-name">{{ $hotelName }}</span><br>
            @if(optional($company)->number)<b>RUC:</b> {{ $company->number }} {{ optional($company)->name }}<br>@endif
            @if(optional($establishment)->address && $establishment->address !== '-')<b>Dirección fiscal:</b> {{ $establishment->address }} {{ $location }}<br>@endif
            @if(optional($establishment)->trade_address && $establishment->trade_address !== '-')<b>Dirección comercial:</b> {{ $establishment->trade_address }} {{ $location }}<br>@endif
            @if(optional($establishment)->telephone)<b>Teléfono:</b> {{ $establishment->telephone }}@endif
            @if(optional($establishment)->email) &nbsp; <b>Correo:</b> {{ $establishment->email }}@endif
        </td>
    </tr>
</table>

<div class="title" style="margin-top: 8px;">{{ $title }}</div>

<table class="list">
    <thead>
        <tr>
            <th width="4%">N°</th>
            <th width="25%">NOMBRES Y APELLIDOS</th>
            <th width="13%">DOCUMENTO</th>
            <th width="12%">CHECK-IN</th>
            <th width="12%">CHECK-OUT</th>
            <th width="9%">ESTADÍA</th>
            <th width="10%">CÓDIGO RESERVA</th>
            <th width="15%">HABITACIÓN</th>
        </tr>
    </thead>
    <tbody>
        @foreach($groups as $g)
            @php $span = max(1, count($g['guests'])); $cls = $g['number'] % 2 ? 'odd' : ''; @endphp
            @foreach($g['guests'] as $i => $guest)
                <tr class="{{ $cls }}">
                    @if($i === 0)
                        <td class="n" rowspan="{{ $span }}">{{ $g['number'] }}</td>
                    @endif
                    <td class="{{ $guest['is_main'] ? 'main' : '' }}">{{ $guest['name'] }}</td>
                    <td>{{ $guest['doc'] ? $guest['doc'] . ' ' : '' }}{{ $guest['number'] }}</td>
                    @if($i === 0)
                        <td class="c" rowspan="{{ $span }}">{{ $g['input_date'] }}<br>{{ $g['input_time'] }}</td>
                        <td class="c" rowspan="{{ $span }}">{{ $g['output_date'] }}<br>{{ $g['output_time'] }}</td>
                        <td class="c" rowspan="{{ $span }}">{{ $g['stay'] }}</td>
                        <td class="c" rowspan="{{ $span }}"><b>{{ $g['code'] }}</b></td>
                        <td class="c" rowspan="{{ $span }}">{{ $g['room'] }}<br><span class="muted">{{ strtoupper((string) $g['category']) }}</span></td>
                    @endif
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

<div class="summary">
    <b>Habitaciones:</b> {{ $roomsCount }} &nbsp;·&nbsp; <b>Huéspedes:</b> {{ $guestsCount }}
    @if($reservedBy) &nbsp;·&nbsp; <b>Reservado por:</b> {{ $reservedBy }} @endif
</div>

@if(count($amenities))
    <div class="services">Servicios incluidos: {{ implode(' · ', $amenities) }}</div>
@endif

</body>
</html>
