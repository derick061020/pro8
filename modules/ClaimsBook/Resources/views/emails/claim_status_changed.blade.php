<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Estado de su reclamo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta content="telephone=no" name="format-detection" />
  <meta name="x-apple-disable-message-reformatting" />
  <style type="text/css" data-premailer="ignore">
    @media screen and (max-width: 600px) {
      u+.body { width: 100vw !important; }
      .wrap { width: 100% !important; }
    }
    a[x-apple-data-detectors] {
      color: inherit !important;
      text-decoration: none !important;
      font-family: inherit !important;
    }
  </style>
  <!--[if mso]>
  <style type="text/css">
    body, table, td { font-family: Arial, Helvetica, sans-serif !important; }
    img { -ms-interpolation-mode: bicubic; }
  </style>
  <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f1f5fb;" class="body">
<center>
  <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="background-color:#f1f5fb;">
    <tr>
      <td align="center" valign="top">
        <!--[if (gte mso 9)|(IE)]>
        <table border="0" cellspacing="0" cellpadding="0">
        <tr><td align="center" valign="top" width="640">
        <![endif]-->

        {{-- Texto de previsualización oculto --}}
        <span style="display:none;font-size:1px;color:#f1f5fb;max-height:0;max-width:0;opacity:0;overflow:hidden;">
          Estado de su reclamo : {{ $data['status']['name'] ?? '' }}
        </span>

        <table class="wrap" cellspacing="0" cellpadding="0" role="presentation" style="width:640px;max-width:640px;">
          <tr>
            <td style="padding:0 20px;">

              <!-- CABECERA -->
              <table cellpadding="0" cellspacing="0" style="width:100%;">
                <tr>
                  <td style="padding:24px 0;">
                    <table cellspacing="0" cellpadding="0" style="width:100%;">
                      <tr>
                        <td style="font-family:Arial,Helvetica,sans-serif;font-size:16px;font-weight:700;color:#232e3c;"></td>
                        <td style="text-align:right;"></td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <!-- /CABECERA -->

              <!-- TARJETA PRINCIPAL -->
              <div style="background:#ffffff;border:1px solid #e6ebf1;border-radius:8px;overflow:hidden;">
                <table width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                    <td>
                      <table cellpadding="0" cellspacing="0" width="100%">

                        <!-- ICONO + TÍTULO -->
                        <tr>
                          <td style="padding:32px 32px 8px;text-align:center;" align="center">

                            <!--[if mso]>
                            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" style="height:64px;v-text-anchor:middle;width:64px;" arcsize="100%" stroke="f" fillcolor="{{ $data['status']['color'] ?? '#066FD1' }}">
                              <w:anchorlock/>
                              <center style="color:#ffffff;font-family:Arial,sans-serif;font-size:24px;font-weight:bold;">&#10003;</center>
                            </v:roundrect>
                            <![endif]-->
                            <!--[if !mso]><!-->
                            <table cellspacing="0" cellpadding="0" role="presentation" style="display:inline-table;">
                              <tr>
                                <td valign="middle" align="center" style="width:64px;height:64px;border-radius:50%;background-color:{{ $data['status']['color'] ?? '#066FD1' }};">
                                  <span style="color:#ffffff;font-size:26px;font-family:Arial,sans-serif;line-height:1;">&#10003;</span>
                                </td>
                              </tr>
                            </table>
                            <!--<![endif]-->

                            <h1 style="font-family:Arial,Helvetica,sans-serif;font-size:22px;font-weight:700;color:#232e3c;text-align:center;margin:16px 0 0 0;">
                              Estado de Reclamo Actualizado
                            </h1>

                            <p style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#6c7a8a;text-align:center;margin:10px 0 0 0;">
                              El estado de tu reclamo <strong style="color:#232e3c;">{{ $data['claim']['public_code'] ?? '' }}</strong> ha sido actualizado.
                            </p>

                          </td>
                        </tr>

                        <!-- DETALLES DEL RECLAMO -->
                        <tr>
                          <td style="padding:24px 32px 8px;">
                            <table style="width:100%;border-collapse:collapse;" cellspacing="0" cellpadding="0">

                              <tr style="border-bottom:1px solid #f0f2f5;">
                                <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#6c7a8a;padding:8px 12px 8px 0;white-space:nowrap;width:1%;">Número de reclamo</td>
                                <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#232e3c;padding:8px 0;text-align:right;">{{ $data['claim']['public_code'] ?? '—' }}</td>
                              </tr>

                              <tr style="border-bottom:1px solid #f0f2f5;">
                                <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#6c7a8a;padding:8px 12px 8px 0;white-space:nowrap;width:1%;">Tipo</td>
                                <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#232e3c;padding:8px 0;text-align:right;">{{ $data['claim']['claim_type'] ?? '—' }}</td>
                              </tr>

                              <tr style="border-bottom:1px solid #f0f2f5;">
                                <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#6c7a8a;padding:8px 12px 8px 0;white-space:nowrap;width:1%;">Estado</td>
                                <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;padding:8px 0;text-align:right;">
                                  <span style="display:inline-block;padding:2px 10px;border-radius:12px;background-color:{{ $data['status']['color'] ?? '#066FD1' }};color:#ffffff;font-weight:600;">
                                    {{ $data['status']['name'] ?? '—' }}
                                  </span>
                                </td>
                              </tr>

                              <tr style="border-bottom:1px solid #f0f2f5;">
                                <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#6c7a8a;padding:8px 12px 8px 0;white-space:nowrap;width:1%;">Reclamante</td>
                                <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#232e3c;padding:8px 0;text-align:right;">{{ $data['claim']['name'] ?? '—' }}</td>
                              </tr>

                              <tr style="border-bottom:1px solid #f0f2f5;">
                                <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#6c7a8a;padding:8px 12px 8px 0;white-space:nowrap;width:1%;">Fecha de creación</td>
                                <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#232e3c;padding:8px 0;text-align:right;">{{ $data['claim']['created_at'] ?? '—' }}</td>
                              </tr>

                              <tr>
                                <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#6c7a8a;padding:8px 12px 8px 0;white-space:nowrap;width:1%;">Última actualización</td>
                                <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#232e3c;padding:8px 0;text-align:right;">{{ $data['claim']['updated_at'] ?? '—' }}</td>
                              </tr>

                            </table>
                          </td>
                        </tr>

                        <!-- DETALLE / MOTIVO -->
                        @if(!empty($data['claim']['detail']))
                        <tr>
                          <td style="padding:16px 32px 8px;">
                            <p style="font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:700;color:#6c7a8a;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 8px;">Detalle del reclamo</p>
                            <p style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#232e3c;margin:0;line-height:1.6;">
                              {{ $data['claim']['detail'] }}
                            </p>
                          </td>
                        </tr>
                        @endif

                        <!-- RESOLUCIÓN (si existe) -->
                        @if(!empty($data['resolution']))
                        <tr>
                          <td style="padding:16px 32px 8px;">
                            <p style="font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:700;color:#6c7a8a;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 8px;">Resolución</p>
                            <p style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#232e3c;margin:0;line-height:1.6;padding:12px;background-color:#f8fafc;border-left:3px solid {{ $data['status']['color'] ?? '#066FD1' }};border-radius:0 4px 4px 0;">
                              {{ $data['resolution'] }}
                            </p>
                          </td>
                        </tr>
                        @endif

                      </table>
                    </td>
                  </tr>
                </table>
              </div>
              <!-- /TARJETA PRINCIPAL -->

              <!-- PIE DE PÁGINA -->
              <table cellspacing="0" cellpadding="0" style="width:100%;">
                <tr>
                  <td style="padding:32px 0;">
                    <table style="width:100%;" cellspacing="0" cellpadding="0">
                      <tr>
                        <td style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#6c7a8a;text-align:center;padding-bottom:8px;">
                          Si no reconoces esta actualización, puedes ignorar este correo.
                        </td>
                      </tr>
                      <tr>
                        <td style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#6c7a8a;text-align:center;padding-top:8px;">
                          &copy; {{ date('Y') }} — Todos los derechos reservados.
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <!-- /PIE DE PÁGINA -->

            </td>
          </tr>
        </table>

        <!--[if (gte mso 9)|(IE)]>
        </td></tr></table>
        <![endif]-->
      </td>
    </tr>
  </table>
</center>
</body>
</html>
