<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Envío de link de pago</title>
    <style>
        body {
            color: #000;
        }
        ul {
            list-style: none;
        }
    </style>
</head>
<body>
<p>Su link de pago ha sido generado correctamente, puede revisarlo en: {{ $user_payment_link}}</p>
@php
    use App\CoreFacturalo\Helpers\CompanyDocumentDisplay as PayLinkCo;
@endphp
<ul>
    @if(PayLinkCo::namesAreSame($company))
    <li>Razón social: {{ PayLinkCo::commercialLine($company) }}</li>
    @else
    <li>Nombre comercial: {{ PayLinkCo::commercialLine($company) }}</li>
    <li>Razón social: {{ $company->name }}</li>
    @endif
    <li>RUC: {{ $company->number }}</li>
</ul>
</body>
</html>