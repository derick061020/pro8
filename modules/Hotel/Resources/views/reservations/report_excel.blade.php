<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
    <title>Reporte de reservas</title>
</head>
<body>
    <div>
        <h3 align="center"><strong>Reporte de reservas</strong></h3>
    </div>
    <br>
    <table>
        <tr>
            <td><b>Empresa:</b></td>
            <td>{{ $company->name ?? '' }}</td>
            <td><b>RUC:</b></td>
            <td>{{ $company->number ?? '' }}</td>
        </tr>
        <tr>
            <td><b>Establecimiento:</b></td>
            <td colspan="3">{{ $establishment->description ?? '' }}{{ isset($establishment->address) ? ' - '.$establishment->address : '' }}</td>
        </tr>
        <tr>
            <td><b>Generado:</b></td>
            <td colspan="3">{{ date('d/m/Y H:i') }}</td>
        </tr>
        @foreach($filters as $label => $value)
            <tr>
                <td><b>{{ $label }}:</b></td>
                <td colspan="3">{{ $value }}</td>
            </tr>
        @endforeach
    </table>
    <br>

    @if(count($records))
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Reserva</th>
                    <th>Estado</th>
                    <th>Habitación</th>
                    <th>Tipo habitación</th>
                    <th>Cliente</th>
                    <th>Documento</th>
                    <th>Teléfono</th>
                    <th>Adultos</th>
                    <th>Niños</th>
                    <th>Fecha ingreso</th>
                    <th>Hora ingreso</th>
                    <th>Fecha salida</th>
                    <th>Hora salida</th>
                    <th>Noches</th>
                    <th>Medio de reserva</th>
                    <th>Tarifa</th>
                    <th>Total</th>
                    <th>Pagado</th>
                    <th>Deuda</th>
                    <th>Estado de pago</th>
                    <th>Comprobante</th>
                    <th>Placa</th>
                    <th>Motivo de viaje</th>
                    <th>Observaciones</th>
                    <th>Registrada</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['id'] }}</td>
                        <td>{{ $row['status'] }}</td>
                        <td>{{ $row['room'] }}</td>
                        <td>{{ $row['category'] }}</td>
                        <td>{{ $row['customer'] }}</td>
                        <td>{{ $row['customer_number'] }}</td>
                        <td>{{ $row['customer_telephone'] }}</td>
                        <td>{{ $row['adults'] }}</td>
                        <td>{{ $row['children'] }}</td>
                        <td>{{ $row['input_date'] }}</td>
                        <td>{{ $row['input_time'] }}</td>
                        <td>{{ $row['output_date'] }}</td>
                        <td>{{ $row['output_time'] }}</td>
                        <td>{{ $row['duration'] }}</td>
                        <td>{{ $row['origin'] }}</td>
                        <td>{{ number_format($row['rental_price'], 2) }}</td>
                        <td>{{ number_format($row['total'], 2) }}</td>
                        <td>{{ number_format($row['paid'], 2) }}</td>
                        <td>{{ number_format($row['debt'], 2) }}</td>
                        <td>{{ $row['payment_state'] }}</td>
                        <td>{{ $row['document_number'] }}</td>
                        <td>{{ $row['license_plate'] }}</td>
                        <td>{{ $row['travel_reason'] }}</td>
                        <td>{{ $row['notes'] }}</td>
                        <td>{{ $row['created_at'] }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="14"></td>
                    <td><b>{{ $totals['nights'] ?? 0 }}</b></td>
                    <td colspan="2"><b>TOTALES</b></td>
                    <td><b>{{ number_format($totals['total'] ?? 0, 2) }}</b></td>
                    <td><b>{{ number_format($totals['paid'] ?? 0, 2) }}</b></td>
                    <td><b>{{ number_format($totals['debt'] ?? 0, 2) }}</b></td>
                    <td colspan="6"></td>
                </tr>
            </tbody>
        </table>
    @else
        <div>
            <p>No se encontraron reservas con los filtros seleccionados.</p>
        </div>
    @endif
</body>
</html>
