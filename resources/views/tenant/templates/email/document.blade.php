<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Comprobante de Pago Electrónico</title>
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
        }
    </style>
    <!--[if mso]>
    <style type="text/css">
        body, table, td { font-family: Arial, Helvetica, sans-serif !important; }
    </style>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f1f5fb;" class="body">
<center>
    <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="background-color:#f1f5fb;">
        <tr>
            <td align="center" valign="top">
                {{-- Texto de previsualización oculto --}}
                <span style="display:none;font-size:1px;color:#f1f5fb;max-height:0;max-width:0;opacity:0;overflow:hidden;">
                    @php
                        use App\CoreFacturalo\Helpers\CompanyDocumentDisplay as EmailDocCompany;
                        $establishment = App\Models\Tenant\Establishment::find($document->establishment_id);
                    @endphp
                    Su comprobante {{ $document->series }}-{{ $document->number }} ha sido emitido exitosamente
                </span>

                <table class="wrap" cellspacing="0" cellpadding="0" role="presentation" style="width:640px;max-width:640px;">
                    <tr>
                        <td style="padding:0 20px;">

                            <!-- TARJETA PRINCIPAL -->
                            <div style="background:#ffffff;border:1px solid #e6ebf1;border-radius:8px;overflow:hidden;margin-top:24px;">
                                <table width="100%" cellpadding="0" cellspacing="0">

                                    <!-- CABECERA AZUL -->
                                    <tr>
                                        <td style="background:#066FD1;padding:24px 32px;text-align:center;">
                                            <p style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:700;color:#ffffff;letter-spacing:1px;text-transform:uppercase;">
                                                Comprobante Emitido
                                            </p>
                                            <h1 style="margin:8px 0 0;font-family:Arial,Helvetica,sans-serif;font-size:22px;font-weight:700;color:#ffffff;">
                                                {{ $document->series }}-{{ $document->number }}
                                            </h1>
                                        </td>
                                    </tr>

                                    <!-- SALUDO -->
                                    <tr>
                                        <td style="padding:28px 32px 8px;">
                                            <p style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:15px;color:#232e3c;">
                                                Estimad@
                                                <strong>
                                                    @if($document->customer)
                                                        {{ $document->customer->name }}
                                                    @else
                                                        {{ $document->supplier->name }}
                                                    @endif
                                                </strong>,
                                            </p>
                                            <p style="margin:10px 0 0;font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#555e6c;line-height:1.6;">
                                                Informamos que su comprobante electrónico ha sido emitido exitosamente. A continuación los datos del comprobante:
                                            </p>
                                        </td>
                                    </tr>

                                    <!-- DATOS DEL COMPROBANTE -->
                                    <tr>
                                        <td style="padding:16px 32px 24px;">
                                            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e6ebf1;border-radius:6px;overflow:hidden;">

                                                @php

                                                    $establishment = App\Models\Tenant\Establishment::find($document->establishment_id);
                                                @endphp

                                                <tr style="background:#f7f9fc;">
                                                    <td style="padding:10px 16px;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700;color:#8898a9;text-transform:uppercase;letter-spacing:0.5px;width:40%;">
                                                        Empresa
                                                    </td>
                                                    <td style="padding:10px 16px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#232e3c;font-weight:700;">
                                                        @if(EmailDocCompany::namesAreSame($company))
                                                            {{ EmailDocCompany::commercialLine($company) }}
                                                        @else
                                                            {{ EmailDocCompany::commercialLine($company) }}
                                                        @endif
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="padding:10px 16px;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700;color:#8898a9;text-transform:uppercase;letter-spacing:0.5px;border-top:1px solid #f0f1f3;">
                                                        Teléfono
                                                    </td>
                                                    <td style="padding:10px 16px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#232e3c;border-top:1px solid #f0f1f3;">
                                                        {{ $establishment->telephone ?? '-' }}
                                                    </td>
                                                </tr>

                                                <tr style="background:#f7f9fc;">
                                                    <td style="padding:10px 16px;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700;color:#8898a9;text-transform:uppercase;letter-spacing:0.5px;border-top:1px solid #f0f1f3;">
                                                        Fecha de emisión
                                                    </td>
                                                    <td style="padding:10px 16px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#232e3c;border-top:1px solid #f0f1f3;">
                                                        {{ $document->date_of_issue->format('d/m/Y') }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="padding:10px 16px;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700;color:#8898a9;text-transform:uppercase;letter-spacing:0.5px;border-top:1px solid #f0f1f3;">
                                                        Nro. comprobante
                                                    </td>
                                                    <td style="padding:10px 16px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#232e3c;font-weight:700;border-top:1px solid #f0f1f3;">
                                                        {{ $document->series }}-{{ $document->number }}
                                                    </td>
                                                </tr>

                                                <tr style="background:#f7f9fc;">
                                                    <td style="padding:10px 16px;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700;color:#8898a9;text-transform:uppercase;letter-spacing:0.5px;border-top:1px solid #f0f1f3;">
                                                        Total
                                                    </td>
                                                    <td style="padding:10px 16px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#232e3c;font-weight:700;border-top:1px solid #f0f1f3;">
                                                        {{ $document->total }}
                                                    </td>
                                                </tr>

                                            </table>
                                        </td>
                                    </tr>

                                    <!-- NOTA FINAL -->
                                    <tr>
                                        <td style="padding:0 32px 28px;">
                                            <p style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#8898a9;line-height:1.6;">
                                                Guardar este correo como comprobante de la transacción. En caso de consultas, comuníquese con el proveedor del servicio.
                                            </p>
                                        </td>
                                    </tr>

                                </table>
                            </div>
                            <!-- /TARJETA PRINCIPAL -->

                            <!-- PIE -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;margin-bottom:24px;">
                                <tr>
                                    <td style="text-align:center;font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#aab4c4;">
                                        Este correo fue generado automáticamente. Por favor no responder.
                                    </td>
                                </tr>
                            </table>
                            <!-- /PIE -->

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</center>
</body>
</html>