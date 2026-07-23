<?php

use Illuminate\Support\Facades\Route;

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if ($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth', 'locked.tenant'])->group(function () {
            Route::prefix('document-form-layout')->group(function () {
                Route::get('{variant}', 'DocumentFormLayoutController@show')->name('tenant.document-form-layout.show');
                Route::put('{variant}', 'DocumentFormLayoutController@update')->name('tenant.document-form-layout.update');
            });
        });
    });
}
