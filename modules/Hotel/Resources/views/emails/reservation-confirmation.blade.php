@extends('hotel::emails.layout')

@section('title', 'Confirmación de reserva')
@section('preheader', 'Recibimos tu reserva ' . $reservation['code'] . ' en ' . ($hotel['name'] ?? 'nuestro hotel'))
@section('header_subtitle', 'Comprobante de reserva')

@section('content')
    @php $accent = $hotel['accent'] ?? '#1abc9c'; @endphp

    <p style="margin:0 0 8px; font-size:16px; color:#1f2933;">
        Hola {{ $reservation['guest_name'] }},
    </p>
    <p style="margin:0 0 20px; font-size:14px; color:#52606d; line-height:1.7;">
        ¡Gracias por reservar con nosotros! Hemos registrado tu solicitud correctamente.
        A continuación tienes el detalle de tu reserva. Nuestro equipo se pondrá en
        contacto contigo para confirmar los últimos detalles y coordinar el pago.
    </p>

    @include('hotel::emails.partials._details', ['reservation' => $reservation, 'accent' => $accent])

    @if(!empty($reservation['notes']))
        <p style="margin:20px 0 4px; font-size:13px; color:#7b8794;">Tus comentarios:</p>
        <p style="margin:0 0 8px; font-size:14px; color:#1f2933; background-color:#f7f9fb; border-left:3px solid {{ $accent }}; padding:10px 14px; border-radius:0 6px 6px 0;">
            {{ $reservation['notes'] }}
        </p>
    @endif

    <p style="margin:24px 0 0; font-size:14px; color:#52606d; line-height:1.7;">
        Si necesitas modificar o cancelar tu reserva, o tienes cualquier consulta,
        responde a este correo o comunícate con nosotros
        @if(!empty($hotel['phone'])) al <strong>{{ $hotel['phone'] }}</strong>@endif.
    </p>

    <p style="margin:20px 0 0; font-size:14px; color:#1f2933;">
        ¡Te esperamos!<br>
        <strong>{{ $hotel['name'] ?? 'El equipo del hotel' }}</strong>
    </p>
@endsection
