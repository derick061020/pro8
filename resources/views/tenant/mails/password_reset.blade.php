<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Recuperar contraseña</title>
  <style type="text/css">
    body {
      margin: 0;
      padding: 0;
      width: 100% !important;
      background: #f3f5f7;
      color: #1f2937;
      font-family: Arial, Helvetica, sans-serif;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    }

    img {
      border: 0;
      outline: none;
      text-decoration: none;
      display: block;
    }

    a {
      color: #1d4ed8;
    }

    .wrapper {
      width: 100%;
      background: #f3f5f7;
      padding: 40px 16px;
    }

    .card {
      width: 100%;
      max-width: 640px;
      margin: 0 auto;
      background: #ffffff;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
      border: 1px solid #e5e7eb;
    }

    .card-header {
      padding: 32px 40px 20px;
      text-align: center;
      background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
      border-bottom: 1px solid #eef2f7;
    }

    .logo {
      max-width: 220px;
      max-height: 72px;
      margin: 0 auto;
    }

    .card-body {
      padding: 32px 40px 40px;
    }

    .eyebrow {
      margin: 0 0 10px;
      font-size: 12px;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: #64748b;
    }

    h1 {
      margin: 0 0 14px;
      font-size: 28px;
      line-height: 1.2;
      color: #0f172a;
      font-weight: 700;
    }

    p {
      margin: 0 0 16px;
      font-size: 15px;
      line-height: 1.7;
      color: #334155;
    }

    .button-row {
      text-align: center;
      margin: 28px 0 30px;
    }

    .button {
      display: inline-block;
      padding: 14px 28px;
      border-radius: 10px;
      background: #1d4ed8;
      color: #ffffff !important;
      text-decoration: none;
      font-size: 15px;
      font-weight: 700;
      letter-spacing: 0.01em;
      box-shadow: 0 8px 18px rgba(29, 78, 216, 0.22);
    }

    .muted-box {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 16px 18px;
      color: #475569;
      font-size: 13px;
      line-height: 1.6;
      word-break: break-word;
    }

    .footer {
      padding: 0 40px 34px;
      text-align: center;
      color: #64748b;
      font-size: 13px;
      line-height: 1.6;
    }

    @media only screen and (max-width: 600px) {
      .wrapper {
        padding: 20px 10px;
      }

      .card-header,
      .card-body,
      .footer {
        padding-left: 22px !important;
        padding-right: 22px !important;
      }

      h1 {
        font-size: 24px;
      }
    }
  </style>
</head>

<body>
  <table role="presentation" class="wrapper" width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
      <td align="center">
        <table role="presentation" class="card" width="100%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td class="card-header">
              @php
                $logoImage = !empty($logoPath) && isset($message) ? $message->embed($logoPath) : $logoSrc;
              @endphp

              @if(!empty($logoImage))
                <img src="{{ $logoImage }}" alt="{{ $company->trade_name ?? 'Logo' }}" class="logo">
              @endif
            </td>
          </tr>
          <tr>
            <td class="card-body">
              <p class="eyebrow">Restablecimiento de contraseña</p>
              <h1>Solicitud de acceso</h1>
              <p>Hemos recibido una solicitud para restablecer la contraseña de su cuenta en {{ $company->trade_name ?? config('app.name') }}.</p>
              <p>Si usted realizó esta solicitud, haga clic en el siguiente botón para crear una nueva contraseña. El enlace expirará en 60 minutos.</p>

              <div class="button-row">
                <a class="button" href="{{ $url }}">Restablecer contraseña</a>
              </div>
            </td>
          </tr>
          <tr>
            <td class="footer">
              Si usted no solicitó este cambio, puede ignorar este correo.
              <br><br>
              Saludos,<br>
              {{ $company->trade_name ?? config('app.name') }}
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>

</html>