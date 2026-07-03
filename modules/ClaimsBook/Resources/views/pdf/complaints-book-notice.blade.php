<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: Arial, Helvetica, sans-serif;
        background: #fff;
        color: #222;
    }

    .aviso {
        width: 100%;
        border: 2px solid #222;
        padding: 30px 40px;
        text-align: center;
    }

    .aviso-title {
        font-size: 55px;
        font-weight: bold;
        color: #007bff;
        line-height: 1.15;
        margin-bottom: 18px;
        letter-spacing: -0.5px;
    }

    .aviso-book {
        display: block;
        margin: 0 auto 18px;
        width: 300px;
    }

    .aviso-legal {
        font-size: 20px;
        color: #222;
        line-height: 1.65;
        margin-bottom: 6px;
        max-width: 420px;
        margin-left: auto;
        margin-right: auto;
    }

    .aviso-scan {
        font-size: 13.5px;
        font-weight: 700;
        color: #222;
        margin-bottom: 16px;
    }

    .aviso-qr {
        display: block;
        margin: 0 auto 10px;
    }

    .aviso-url {
        font-size: 12px;
        color: #007bff;
        margin-bottom: 18px;
        word-break: break-all;
    }

    .aviso-divider {
        border: none;
        border-top: 1px solid #ccc;
        margin: 14px 0;
    }

    .aviso-company {
        font-size: 12px;
        font-weight: 700;
        color: #222;
        margin-bottom: 4px;
    }

    .aviso-indecopi {
        font-size: 11px;
        color: #555;
    }

    .aviso-indecopi a {
        color: #d00;
        text-decoration: none;
    }
</style>
</head>
<body>
<div class="aviso">

    <div class="aviso-title">Libro de<br>Reclamaciones</div>

    <img class="aviso-book" src="{{ $bookImageSrc }}" alt="Libro">

    <p class="aviso-legal">
        Conforme a lo establecido en el Código de Protección y Defensa del Consumidor,
        este establecimiento cuenta con un Libro de Reclamaciones Virtual a tu disposición.
        Solicítalo para registrar una queja o reclamo.
    </p>

    <p class="aviso-scan">Escanea el siguiente código QR para registrar una queja o reclamo</p>

    <barcode code="{{ $widgetUrl }}" type="QR" size="1.4" error="M" disableborder="1" class="aviso-qr" />

    <p class="aviso-url">{{ $widgetUrl }}</p>

    <hr class="aviso-divider">

    <p class="aviso-company">{{ $companyName }}</p>
    <p class="aviso-indecopi">
        En caso de negativa de entrega del libro escribe a
        <a href="mailto:libroreclamaciones@indecopi.gob.pe">libroreclamaciones@indecopi.gob.pe</a>
    </p>

</div>
</body>
</html>
