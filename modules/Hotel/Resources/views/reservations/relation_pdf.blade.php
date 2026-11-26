@php
    // Íconos SVG simples (trazo blanco) incrustados como data URI para mPDF.
    $navy = '#14295b';
    $gold = '#c9a227';
    $glyphs = [
        'pin'      => '<path d="M12 21s-7-6.5-7-11a7 7 0 0 1 14 0c0 4.5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
        'doc'      => '<path d="M6 3h8l4 4v14H6z"/><path d="M9 12h6M9 16h6"/>',
        'building' => '<path d="M5 21V4h14v17M3 21h18"/><path d="M9 8h2M13 8h2M9 12h2M13 12h2M10 21v-4h4v4"/>',
        'phone'    => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/>',
        'mail'     => '<rect x="3" y="5" width="18" height="14" rx="1.5"/><path d="M3 7l9 6 9-6"/>',
        'wifi'     => '<path d="M2 9a15 15 0 0 1 20 0M5 12.5a10 10 0 0 1 14 0M8.5 16a5 5 0 0 1 7 0"/><circle cx="12" cy="19.5" r="1.2" fill="#fff"/>',
        'tv'       => '<rect x="3" y="5" width="18" height="12" rx="1.5"/><path d="M8 21h8M12 17v4"/>',
        'bath'     => '<path d="M4 12h16v3a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4z"/><path d="M6 12V6a2 2 0 0 1 4 0M7 19l-1 2M17 19l1 2"/>',
        'water'    => '<path d="M12 3s6 7 6 11a6 6 0 0 1-12 0c0-4 6-11 6-11z"/>',
        'desk'     => '<path d="M3 8h18M5 8v12M19 8v12M14 8v6h5"/>',
        'bed'      => '<path d="M3 18V7M3 13h18v5M21 13a3 3 0 0 0-3-3h-7v3"/><circle cx="7" cy="10.5" r="1.5"/>',
        'check'    => '<path d="M5 12l5 5 9-10"/>',
    ];
    // $circle: ícono blanco dentro de un círculo azul (cabecera).
    $icon = function ($name, $circle = false) use ($glyphs, $navy) {
        $g = $glyphs[$name] ?? $glyphs['check'];
        $body = '<g fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $g . '</g>';
        $svg = $circle
            ? '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="12" fill="' . $navy . '"/><g transform="translate(5 5) scale(0.5833)">' . $body . '</g></svg>'
            : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">' . $body . '</svg>';
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    };
    $amenityIcon = function ($text) {
        $t = mb_strtolower($text);
        if (strpos($t, 'wi') !== false) return 'wifi';
        if (strpos($t, 'tv') !== false || strpos($t, 'cable') !== false || strpos($t, 'televis') !== false) return 'tv';
        if (strpos($t, 'baño') !== false || strpos($t, 'ducha') !== false) return 'bath';
        if (strpos($t, 'agua') !== false) return 'water';
        if (strpos($t, 'escritorio') !== false) return 'desk';
        if (strpos($t, 'cama') !== false || strpos($t, 'habitaci') !== false) return 'bed';
        return 'check';
    };
    $services = array_slice(array_values($amenities), 0, 6);
@endphp
<html>
<head>
<style>
    body { font-family: dejavusanscondensed, sans-serif; font-size: 9pt; color: #1f2937; }
    .hotel-name { font-size: 14pt; font-weight: bold; color: #14295b; }
    .hd td { font-size: 9pt; color: #14295b; padding: 1px 0; vertical-align: middle; }
    .hd b { color: #14295b; }
    .title { background: #14295b; color: #fff; font-size: 12pt; font-weight: bold; text-align: center; padding: 6px; border-bottom: 2px solid #c9a227; }
    table.list { border-collapse: collapse; width: 100%; }
    table.list th { background: #14295b; color: #fff; font-size: 9pt; padding: 6px 4px; border: 1px solid #0d1d42; text-align: center; }
    table.list td { border: 1px solid #b9c8dd; padding: 6px 5px; font-size: 9pt; vertical-align: middle; color: #14295b; }
    tr.odd td { background: #dce8f6; }
    td.c { text-align: center; }
    td.n { text-align: center; font-weight: bold; font-size: 10pt; }
    .main { font-weight: bold; }
    .muted { color: #4b5d7a; }
    .summary { margin-top: 8px; font-size: 8.5pt; color: #14295b; }
</style>
</head>
<body>

<htmlpagefooter name="relfooter">
    <table width="100%" cellspacing="0" cellpadding="0" style="background: #14295b; border-top: 3px solid #c9a227;">
        <tr>
            <td style="padding: 8px 14px; vertical-align: middle;">
                <span style="font-family: dejavuserifcondensed; font-style: italic; font-size: 17pt; color: #ffffff;">Tu descanso, nuestra prioridad</span>
            </td>
            @foreach($services as $s)
                <td width="9%" style="text-align: center; vertical-align: middle; padding: 6px 3px; border-left: 1px solid #c9a227; color: #ffffff; font-size: 7pt; font-weight: bold;">
                    <img src="{{ $icon($amenityIcon($s)) }}" style="width: 20px; height: 20px;"><br>{{ $s }}
                </td>
            @endforeach
        </tr>
    </table>
    <div style="font-size: 7pt; color: #888; text-align: right; padding-top: 2px;">Generado {{ $generatedAt }} · Página {PAGENO} de {nbpg}</div>
</htmlpagefooter>
<sethtmlpagefooter name="relfooter" value="on" />

<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        @if($logoPath)
            <td width="24%" style="vertical-align: middle; padding-right: 12px;"><img src="{{ $logoPath }}" style="max-height: 80px; max-width: 220px;"></td>
        @endif
        <td style="vertical-align: middle; border-left: 2px solid #c9a227; padding-left: 14px;">
            <table class="hd" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="26"><img src="{{ $icon('pin', true) }}" style="width: 18px; height: 18px;"></td>
                    <td><span class="hotel-name">{{ $hotelName }}</span>@if(optional($company)->number)<br><b>RUC:</b> {{ $company->number }} {{ optional($company)->name }}@endif</td>
                </tr>
                @if(optional($establishment)->address && $establishment->address !== '-')
                    <tr>
                        <td><img src="{{ $icon('doc', true) }}" style="width: 18px; height: 18px;"></td>
                        <td><b>Dirección fiscal:</b> {{ $establishment->address }} {{ $location }}</td>
                    </tr>
                @endif
                @if(optional($establishment)->trade_address && $establishment->trade_address !== '-')
                    <tr>
                        <td><img src="{{ $icon('building', true) }}" style="width: 18px; height: 18px;"></td>
                        <td><b>Dirección comercial:</b> {{ $establishment->trade_address }} {{ $location }}</td>
                    </tr>
                @endif
                @if(optional($establishment)->telephone)
                    <tr>
                        <td><img src="{{ $icon('phone', true) }}" style="width: 18px; height: 18px;"></td>
                        <td><b>Teléfono:</b> {{ $establishment->telephone }}</td>
                    </tr>
                @endif
                @if(optional($establishment)->email)
                    <tr>
                        <td><img src="{{ $icon('mail', true) }}" style="width: 18px; height: 18px;"></td>
                        <td><b>Correo:</b> {{ $establishment->email }}</td>
                    </tr>
                @endif
            </table>
        </td>
    </tr>
</table>

<table width="100%" cellspacing="0" cellpadding="0" style="margin: 8px 0;"><tr><td style="background: #c9a227; height: 2px; font-size: 1pt;">&nbsp;</td></tr></table>

<div class="title">{{ $title }}</div>

<table class="list">
    <thead>
        <tr>
            <th width="4%">N°</th>
            <th width="31%">NOMBRES Y APELLIDOS</th>
            <th width="16%">DOCUMENTO</th>
            <th width="10%">CHECK-IN</th>
            <th width="10%">CHECK-OUT</th>
            <th width="8%">ESTADÍA</th>
            <th width="9%">CÓDIGO RESERVA</th>
            <th width="12%">HABITACIÓN</th>
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

</body>
</html>
