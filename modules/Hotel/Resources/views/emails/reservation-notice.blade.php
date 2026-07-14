@extends('hotel::emails.layout')

@section('title', 'Nueva reserva web')
@section('preheader', 'Nueva reserva ' . $reservation['code'] . ' desde la web')
@section('header_subtitle', 'Nueva reserva desde la web')

@section('content')
    @php $accent = $hotel['accent'] ?? '#1abc9c'; @endphp

    <p style="margin:0 0 20px; font-size:14px; color:#52606d; line-height:1.7;">
        Se ha registrado una <strong>nueva reserva desde la web</strong>. Revísala en
        recepción / calendario para confirmarla.
    </p>

    @include('hotel::emails.partials._details', ['reservation' => $reservation, 'accent' => $accent])

    <p style="margin:24px 0 8px; font-size:13px; color:#7b8794; text-transform:uppercase; letter-spacing:0.5px;">
        Datos del huésped
    </p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="border:1px solid #eceff2; border-radius:10px; overflow:hidden;">
        <tr style="background-color:#ffffff;">
            <td style="padding:12px 16px; font-size:13px; color:#7b8794; width:42%; border-bottom:1px solid #eceff2;">Nombre</td>
            <td style="padding:12px 16px; font-size:14px; color:#1f2933; font-weight:600; border-bottom:1px solid #eceff2;">{{ $reservation['guest_name'] }}</td>
        </tr>
        @if(!empty($reservation['guest_document']))
        <tr style="background-color:#f7f9fb;">
            <td style="padding:12px 16px; font-size:13px; color:#7b8794; border-bottom:1px solid #eceff2;">Documento</td>
            <td style="padding:12px 16px; font-size:14px; color:#1f2933; font-weight:600; border-bottom:1px solid #eceff2;">{{ $reservation['guest_document'] }}</td>
        </tr>
        @endif
        @if(!empty($reservation['guest_phone']))
        <tr style="background-color:#ffffff;">
            <td style="padding:12px 16px; font-size:13px; color:#7b8794; border-bottom:1px solid #eceff2;">Teléfono</td>
            <td style="padding:12px 16px; font-size:14px; color:#1f2933; font-weight:600; border-bottom:1px solid #eceff2;">{{ $reservation['guest_phone'] }}</td>
        </tr>
        @endif
        @if(!empty($reservation['guest_email']))
        <tr style="background-color:#f7f9fb;">
            <td style="padding:12px 16px; font-size:13px; color:#7b8794;">Correo</td>
            <td style="padding:12px 16px; font-size:14px; color:#1f2933; font-weight:600;">{{ $reservation['guest_email'] }}</td>
        </tr>
        @endif
    </table>

    @if(!empty($reservation['notes']))
        <p style="margin:20px 0 4px; font-size:13px; color:#7b8794;">Comentarios del huésped:</p>
        <p style="margin:0; font-size:14px; color:#1f2933; background-color:#f7f9fb; border-left:3px solid {{ $accent }}; padding:10px 14px; border-radius:0 6px 6px 0;">
            {{ $reservation['notes'] }}
        </p>
    @endif
@endsection
