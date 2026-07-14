{{--
    Layout base para los correos del hotel.

    Diseño 100% en tablas + estilos inline para máxima compatibilidad con
    clientes de correo (Gmail, Outlook, Apple Mail, etc.). El color de acento
    (`$accent`) proviene de la personalización de la web del hotel.

    Variables esperadas:
      $hotel  = ['name','address','email','phone','accent']
      Secciones: @section('title'), @section('preheader'), @yield('content')
--}}
@php
    $accent   = $hotel['accent'] ?? '#1abc9c';
    $hotelName = $hotel['name'] ?? 'Hotel';
@endphp
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', $hotelName)</title>
</head>
<body style="margin:0; padding:0; background-color:#eef1f4; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">
    {{-- Preheader oculto (texto de vista previa en la bandeja) --}}
    <div style="display:none; font-size:1px; color:#eef1f4; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden;">
        @yield('preheader', 'Información de tu reserva en ' . $hotelName)
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#eef1f4;">
        <tr>
            <td align="center" style="padding:24px 12px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px; max-width:600px; background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.06); font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

                    {{-- Encabezado con el color del hotel --}}
                    <tr>
                        <td style="background-color:{{ $accent }}; padding:28px 32px; text-align:center;">
                            <div style="color:#ffffff; font-size:22px; font-weight:700; letter-spacing:0.3px;">
                                {{ $hotelName }}
                            </div>
                            @hasSection('header_subtitle')
                                <div style="color:rgba(255,255,255,0.9); font-size:13px; margin-top:6px;">
                                    @yield('header_subtitle')
                                </div>
                            @endif
                        </td>
                    </tr>

                    {{-- Contenido --}}
                    <tr>
                        <td style="padding:32px;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Pie con datos de contacto --}}
                    <tr>
                        <td style="background-color:#f7f9fb; padding:24px 32px; border-top:1px solid #eceff2;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#7b8794; font-size:12px; line-height:1.7;">
                                        <strong style="color:#3e4c59;">{{ $hotelName }}</strong><br>
                                        @if(!empty($hotel['address']))
                                            {{ $hotel['address'] }}<br>
                                        @endif
                                        @if(!empty($hotel['phone']))
                                            Tel: {{ $hotel['phone'] }}
                                        @endif
                                        @if(!empty($hotel['email']))
                                            @if(!empty($hotel['phone'])) &nbsp;·&nbsp; @endif
                                            {{ $hotel['email'] }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>

                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px; max-width:600px;">
                    <tr>
                        <td style="padding:16px 8px; text-align:center; font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#9aa5b1; font-size:11px;">
                            Este es un correo automático, por favor no respondas directamente.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
