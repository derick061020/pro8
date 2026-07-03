<?php

use Illuminate\Support\Facades\Route;

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if ($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth', 'locked.tenant'])->group(function () {
            Route::prefix('item-form-layout')->group(function () {
                Route::get('{variant}', 'ItemFormLayoutController@show')->name('tenant.item-form-layout.show');
                Route::put('{variant}', 'ItemFormLayoutController@update')->name('tenant.item-form-layout.update');
            });
        });
    });
}
