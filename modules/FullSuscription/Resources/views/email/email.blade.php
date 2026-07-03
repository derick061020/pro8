<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Envio de suscripción</title>
</head>
<body>
    <h2>
        Estimad@ Cliente
    </h2>
    <p>
        Hemos verificado que su suscripción al plan <strong>{{ $order->suscription->suscription_plan->name }}</strong>
        ha terminado. Si desea renovar su suscripción, haga click en el siguiente enlace de pago:
    </p>
    <a href="{{ $order->getUrl() }}">Enlace de pago</a>
</body>
</html>
