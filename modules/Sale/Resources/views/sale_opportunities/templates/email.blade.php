<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Envio de o. venta</title>
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
    use App\CoreFacturalo\Helpers\CompanyDocumentDisplay as EmailOppCo;
@endphp
<p>Estimad@: 

    {{ EmailOppCo::commercialLine($company) }}
  
    , informamos que su o. venta ha sido emitido exitosamente.</p>

<ul>

</ul>
</body>
</html>