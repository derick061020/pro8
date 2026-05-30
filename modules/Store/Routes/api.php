<?php

use Illuminate\Support\Facades\Route;

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth:api', 'locked.tenant'])->group(function () {
            Route::prefix('store')->group(function () {
                Route::post('get_igv', 'StoreController@getIgv');
            });
        });
    });
}