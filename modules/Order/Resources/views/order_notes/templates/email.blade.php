<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Envio de pedido</title>
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
    use App\CoreFacturalo\Helpers\CompanyDocumentDisplay as EmailOrdNoteCo;
@endphp
<p>Estimad@: 

    {{ EmailOrdNoteCo::commercialLine($company) }}
  
    , informamos que su pedido ha sido emitido exitosamente.</p>

<ul>

</ul>
</body>
</html>