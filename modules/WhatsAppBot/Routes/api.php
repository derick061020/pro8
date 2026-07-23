<?php

use Illuminate\Support\Facades\Route;

// Sin este wrapper, Laravel nunca identifica el tenant antes de que
// WebhookController toque `Configuration::first()` (conexion `tenant`), y la
// request truena con "Database connection [tenant] not configured" para
// cualquier hostname (ver mismo patron en Modules/QrApi/Routes/api.php).
$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if ($hostname) {
    Route::domain($hostname->fqdn)->group(function () {
        Route::prefix('whatsapp-bot')->group(function () {
            Route::post('webhook/{token}', 'Api\WebhookController@receive')->name('whatsapp-bot.webhook');
        });
    });
}
