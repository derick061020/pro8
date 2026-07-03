<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Envio de reporte de caja</title>
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
    use App\CoreFacturalo\Helpers\CompanyDocumentDisplay as EmailCashRevCo;
@endphp
<p>Estimad@: 

    {{ EmailCashRevCo::commercialLine($company) }}
  
    , informamos que su reporte de caja ha sido generado exitosamente.</p>

<ul>

</ul>
</body>
</html>