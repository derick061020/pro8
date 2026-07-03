<?php

use Illuminate\Support\Facades\Route;

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if ($hostname) {
    Route::domain($hostname->fqdn)->group(function () {
        // Registro de reclamo desde el widget (rate limiting: 10 peticiones/minuto por IP)
        Route::middleware('throttle:10,1')
            ->post('/claims/remote/store', 'ClaimController@publicStore');
    });
}