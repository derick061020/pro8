<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Envio de contrato</title>
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
    use App\CoreFacturalo\Helpers\CompanyDocumentDisplay as EmailContractCo;
@endphp
<p>Estimad@: 

    {{ EmailContractCo::commercialLine($company) }}
  
    , informamos que su contrato ha sido emitido exitosamente.</p>

<ul>

</ul>
</body>
</html>