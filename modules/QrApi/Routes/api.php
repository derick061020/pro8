<?php

use Illuminate\Http\Request;

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if ($hostname)
{
    Route::domain($hostname->fqdn)->group(function () {
        Route::middleware(['auth:api', 'locked.tenant'])->group(function () {
            Route::prefix('qrapi')->group(function() {
                Route::post('/send-message', 'QrApiController@sendMessage');
            });
        });
    });
}