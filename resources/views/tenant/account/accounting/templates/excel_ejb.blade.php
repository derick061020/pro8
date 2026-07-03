<table>
    <tr>
        <th>Ruc</th>
        <th>tipo documento</th>
        <th>serie documento</th>
        <th>numero documento</th>
        <th>fecha documento</th>
        <th>fecha vencimiento</th>
        <th>moneda</th>
        <th>importe igv</th>
        <th>importe documento</th>
        <th>importe inafecto</th>
        <th>importe ISC</th>
        <th>Otros</th>
        <th>impuesto Bolsas</th>
        <th>Cuenta de venta</th>
        <th>Fecha documento Ref</th>
        <th>Tipo documento Ref</th>
        <th>Serie documento Ref</th>
        <th>Número documento Ref</th>
        <th>Centro Costo</th>
        <th>subdiario</th>
        <th>cuenta por Cobrar</th>
        <th>glosa de comprobante</th>
        <th>Anexo de venta</th>
        <th>Comprobante</th>
        <th>Anexo Auxiliar</th>
        <th>Dato Adicional</th>
        <th>Cuenta Pago Automático</th>
        <th>Número Documento Pago Automático</th>
        <th>Importe de Pago Automático</th>
    </tr>
    @foreach($records as $row)
        <tr>
            <td style="mso-number-format:'\@'; white-space:pre;">{{ $row['customer_number'] }}</td>
            <td>{{ $row['document_type'] }}</td>
            <td>{{ $row['series'] }}</td>
            <td style="mso-number-format:'\@';">{{ $row['number'] }}</td>
            <td>{{ $row['date_of_issue_excel'] }}</td>
            <td>{{ $row['date_of_due_excel'] }}</td>
            <td>{{ $row['currency_type_id'] }}</td>
            <td>{{ $row['total_igv'] }}</td>
            <td>{{ $row['total'] }}</td>
            <td>{{ $row['total_unaffected'] }}</td>
            <td>{{ $row['total_isc'] }}</td>
            <td>{{ $row['others'] }}</td>
            <td>{{ $row['total_plastic_bag_taxes'] }}</td>
            <td>{{ $row['income_account'] }}</td>
            <td>{{ $row['ref_date_excel'] }}</td>
            <td>{{ $row['ref_document_type'] }}</td>
            <td>{{ $row['ref_series'] }}</td>
            <td style="mso-number-format:'\@';">{{ $row['ref_number'] }}</td>
            <td>{{ $row['cost_center'] }}</td>
            <td style="mso-number-format:'\@';">{{ $row['subdiary'] }}</td>
            <td>{{ $row['receivable'] }}</td>
            <td>{{ $row['gloss'] }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ $row['automatic_payment_account']}}</td>
            <td>{{ $row['automatic_payment_document_number']}}</td>
            <td>{{ $row['automatic_payment_amount']}}</td>
        </tr>
    @endforeach
</table>