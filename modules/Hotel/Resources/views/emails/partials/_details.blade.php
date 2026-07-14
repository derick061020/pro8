{{--
    Tarjeta con el resumen de la reserva. Reutilizada por el correo de
    confirmación (huésped) y el aviso interno (recepción).

    Variables: $reservation (array), $accent (string hex).
--}}
@php
    $r = $reservation;
    $accent = $accent ?? '#1abc9c';
    $rows = [
        ['Código de reserva', $r['code']],
        ['Habitación',        $r['room']],
        ['Entrada',           $r['checkin_date'] . ' · ' . $r['checkin_time']],
        ['Salida',            $r['checkout_date'] . ' · ' . $r['checkout_time']],
        ['Noches',            $r['nights'] . ($r['nights'] == 1 ? ' noche' : ' noches')],
        ['Huéspedes',         $r['guests_label']],
    ];
    if (!empty($r['payment_method'])) {
        $rows[] = ['Medio de pago', $r['payment_method']];
    }
@endphp

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       style="border:1px solid #eceff2; border-radius:10px; overflow:hidden; margin:8px 0 4px;">
    @foreach($rows as $i => $row)
        <tr style="background-color:{{ $i % 2 === 0 ? '#ffffff' : '#f7f9fb' }};">
            <td style="padding:12px 16px; font-size:13px; color:#7b8794; width:42%; border-bottom:1px solid #eceff2;">
                {{ $row[0] }}
            </td>
            <td style="padding:12px 16px; font-size:14px; color:#1f2933; font-weight:600; border-bottom:1px solid #eceff2;">
                {{ $row[1] }}
            </td>
        </tr>
    @endforeach
    @if(($r['total'] ?? 0) > 0)
        <tr style="background-color:{{ $accent }};">
            <td style="padding:14px 16px; font-size:13px; color:#ffffff;">
                Total estimado
            </td>
            <td style="padding:14px 16px; font-size:18px; color:#ffffff; font-weight:700;">
                {{ $r['currency'] }} {{ number_format($r['total'], 2) }}
            </td>
        </tr>
    @endif
</table>
