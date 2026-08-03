<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API de sincronización offline
|--------------------------------------------------------------------------
|
| Estas rutas las publica la instalación ONLINE y las consume cada terminal
| Windows. El token que manda el terminal es el api_token de un usuario del
| tenant, y además el terminal tiene que estar dado de alta (offline.terminal).
|
| El pareo (handshake) no exige alta previa: justamente sirve para darla.
|
*/

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if ($hostname) {
    Route::domain($hostname->fqdn)->group(function () {
        Route::prefix('offline')->middleware(['auth:api'])->group(function () {

            Route::post('handshake', 'Api\OfflineSyncController@handshake');

            Route::middleware(['offline.terminal'])->group(function () {
                Route::get('ping', 'Api\OfflineSyncController@ping');
                Route::post('push', 'Api\OfflineSyncController@push');
                Route::get('pull', 'Api\OfflineSyncController@pull');
                Route::post('ranges/allocate', 'Api\OfflineSyncController@allocateRange');
                Route::post('ranges/report', 'Api\OfflineSyncController@reportRanges');
            });
        });
    });
}
