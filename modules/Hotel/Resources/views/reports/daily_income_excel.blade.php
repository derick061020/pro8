@php
    $methods = collect($report['methods']);
    $money = fn ($v) => number_format((float) ($v ?? 0), 2, '.', '');
    $methodName = fn ($id) => optional($methods->firstWhere('id', $id))['description'] ?? 'Otros';
@endphp
<table>
    <tr><td colspan="{{ $methods->count() + 5 }}"><b>INGRESOS DIARIOS</b> — del {{ \Carbon\Carbon::parse($report['from'])->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($report['to'])->format('d/m/Y') }}</td></tr>
    <tr><td colspan="{{ $methods->count() + 5 }}">Dinero que entró a caja por método de pago. No descuenta gastos.</td></tr>
    <tr><td></td></tr>

    <tr><td colspan="{{ $methods->count() + 5 }}"><b>POR DÍA</b></td></tr>
    <tr>
        <th><b>Fecha</b></th><th><b>Turnos</b></th><th><b>Hab. ocupadas</b></th><th><b>Hab. cobradas</b></th>
        @foreach($methods as $m)<th><b>{{ $m['description'] }}</b></th>@endforeach
        <th><b>Total</b></th>
    </tr>
    @foreach($report['days'] as $d)
        <tr>
            <td>{{ \Carbon\Carbon::parse($d['date'])->format('d/m/Y') }}</td>
            <td>{{ $d['turns'] }}</td>
            <td>{{ count($d['occupied'] ?? []) }}</td>
            <td>{{ count($d['rooms']) }}</td>
            @foreach($methods as $m)<td>{{ $money($d['by_method'][$m['id']] ?? 0) }}</td>@endforeach
            <td><b>{{ $money($d['total']) }}</b></td>
        </tr>
    @endforeach
    <tr>
        <td><b>TOTAL</b></td><td></td><td></td><td></td>
        @foreach($methods as $m)<td><b>{{ $money($report['totals']['by_method'][$m['id']] ?? 0) }}</b></td>@endforeach
        <td><b>{{ $money($report['totals']['total']) }}</b></td>
    </tr>
    <tr><td></td></tr>

    <tr><td colspan="{{ $methods->count() + 7 }}"><b>POR TURNO DE CAJA</b></td></tr>
    <tr>
        <th><b>Cajero</b></th><th><b>Apertura</b></th><th><b>Cierre</b></th><th><b>Abrió con</b></th>
        @foreach($methods as $m)<th><b>{{ $m['description'] }}</b></th>@endforeach
        <th><b>Total ingresos</b></th><th><b>Habitaciones</b></th>
    </tr>
    @foreach($report['turns'] as $t)
        <tr>
            <td>{{ $t['cashier'] }}</td>
            <td>{{ $t['opened_at'] }}</td>
            <td>{{ $t['open'] ? 'Abierta' : $t['closed_at'] }}</td>
            <td>{{ $money($t['beginning_balance']) }}</td>
            @foreach($methods as $m)<td>{{ $money($t['by_method'][$m['id']] ?? 0) }}</td>@endforeach
            <td><b>{{ $money($t['total']) }}</b></td>
            <td>{{ implode(', ', $t['rooms']) }}</td>
        </tr>
    @endforeach
    <tr><td></td></tr>

    <tr><td colspan="7"><b>DETALLE DE PAGOS</b></td></tr>
    <tr>
        <th><b>Fecha y hora</b></th><th><b>Cajero</b></th><th><b>Concepto</b></th><th><b>Habitación</b></th>
        <th><b>Huésped / cliente</b></th><th><b>Método</b></th><th><b>Monto</b></th>
    </tr>
    @foreach($report['turns'] as $t)
        @foreach($t['detail'] as $p)
            <tr>
                <td>{{ $p['created_at'] ?? $p['time'] }}</td>
                <td>{{ $t['cashier'] }}</td>
                <td>{{ $p['concept'] }}</td>
                <td>{{ $p['room'] }}</td>
                <td>{{ $p['customer'] }}</td>
                <td>{{ $methodName($p['method_id']) }}</td>
                <td>{{ $money($p['amount']) }}</td>
            </tr>
        @endforeach
    @endforeach
</table>
