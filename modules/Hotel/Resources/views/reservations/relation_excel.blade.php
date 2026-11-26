<table>
    <tr><td colspan="8"><b>{{ $hotelName }}</b></td></tr>
    @if(optional($company)->number)
        <tr><td colspan="8">RUC: {{ $company->number }} {{ optional($company)->name }}</td></tr>
    @endif
    @if(optional($establishment)->address && $establishment->address !== '-')
        <tr><td colspan="8">Dirección fiscal: {{ $establishment->address }} {{ $location }}</td></tr>
    @endif
    @if(optional($establishment)->trade_address && $establishment->trade_address !== '-')
        <tr><td colspan="8">Dirección comercial: {{ $establishment->trade_address }} {{ $location }}</td></tr>
    @endif
    @if(optional($establishment)->telephone)
        <tr><td colspan="8">Teléfono: {{ $establishment->telephone }}</td></tr>
    @endif
    <tr><td colspan="8"></td></tr>
    <tr><td colspan="8" style="background-color: #4b5d73; color: #ffffff; font-weight: bold; text-align: center;">{{ $title }}</td></tr>
    <tr>
        @foreach(['N°', 'NOMBRES Y APELLIDOS', 'DOCUMENTO', 'CHECK-IN', 'CHECK-OUT', 'ESTADÍA', 'CÓDIGO RESERVA', 'HABITACIÓN'] as $h)
            <th style="background-color: #4b5d73; color: #ffffff; font-weight: bold; text-align: center;">{{ $h }}</th>
        @endforeach
    </tr>
    @foreach($groups as $g)
        @foreach($g['guests'] as $i => $guest)
            <tr>
                <td style="text-align: center;">{{ $i === 0 ? $g['number'] : '' }}</td>
                <td @if($guest['is_main']) style="font-weight: bold;" @endif>{{ $guest['name'] }}</td>
                <td>{{ $guest['doc'] ? $guest['doc'] . ' ' : '' }}{{ $guest['number'] }}</td>
                <td>{{ $g['input_date'] }} {{ $g['input_time'] }}</td>
                <td>{{ $g['output_date'] }} {{ $g['output_time'] }}</td>
                <td>{{ $g['stay'] }}</td>
                <td>{{ $g['code'] }}</td>
                <td>{{ $g['room'] }} - {{ $g['category'] }}</td>
            </tr>
        @endforeach
    @endforeach
    <tr><td colspan="8"></td></tr>
    <tr><td colspan="8">Habitaciones: {{ $roomsCount }} · Huéspedes: {{ $guestsCount }}@if($reservedBy) · Reservado por: {{ $reservedBy }}@endif</td></tr>
    @if(count($amenities))
        <tr><td colspan="8">Servicios incluidos: {{ implode(' · ', $amenities) }}</td></tr>
    @endif
</table>
