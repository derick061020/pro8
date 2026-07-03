<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Envio de Reporte General de ventas</title>
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
@php
    use App\CoreFacturalo\Helpers\CompanyDocumentDisplay as MobileAppRepCo;
@endphp
<ul>
    <li>RUC/DNI: {{ $company->number}}</li>
    @if(MobileAppRepCo::namesAreSame($company))
    <li>Razón social: {{ MobileAppRepCo::commercialLine($company) }}</li>
    @else
    <li>Nombre comercial: {{ MobileAppRepCo::commercialLine($company) }}</li>
    <li>Razón social: {{ $company->name }}</li>
    @endif
    <li>Teléfono: {{ $establishment->telephone }}</li>
    <li>Fecha : {{ Carbon\Carbon::now()->format('d/m/Y') }}</li>
</ul>
</body>
</html>