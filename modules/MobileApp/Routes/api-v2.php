<?php

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if ($hostname)
{
    Route::domain($hostname->fqdn)->group(function () {

        // V2
        Route::get('app.json', 'Api\AppConfigurationController@app_json')->name('tenant.app_json');

        Route::middleware(['auth:api', 'locked.tenant'])->group(function () {

            Route::prefix('items')->group(function () {
                Route::get('records-scroll', 'Api\ItemController@byScroll');
                Route::post('save', 'Api\ItemController@save');
            });

            Route::prefix('persons')->group(function () {
                Route::post('/create/{type}', 'Api\PersonController@store');
                Route::get('/records-scroll/{type}', 'Api\PersonController@byScroll');
                Route::get('locations', 'Api\PersonController@locations');
            });

            Route::prefix('app-configurations')->group(function () {});

            Route::prefix('quotations')->group(function () {
                Route::get('records-scroll', 'Api\QuotationController@byScroll');
            });

            // factura, boleta, nota de venta
            Route::prefix('documents')->group(function () {
                Route::get('records-scroll', 'Api\DocumentCentralizedController@byScroll');
            });

            // pagos de comprobantes (factura, boleta)
            Route::prefix('document-payments')->group(function () {
                Route::get('tables', 'Api\DocumentPaymentController@tables');
                Route::get('document/{document_id}', 'Api\DocumentPaymentController@document');
                Route::get('records/{document_id}', 'Api\DocumentPaymentController@records');
                Route::post('/', 'Api\DocumentPaymentController@store');
                Route::delete('{id}', 'Api\DocumentPaymentController@destroy');
            });

            // bandeja de pagos recibidos (Yape / Plin) capturados por el equipo-principal
            Route::prefix('received-payments')->group(function () {
                Route::get('records-scroll', 'Api\ReceivedPaymentController@byScroll');
                Route::post('/', 'Api\ReceivedPaymentController@store');
                Route::post('{id}/claim', 'Api\ReceivedPaymentController@claim');
                Route::put('{id}', 'Api\ReceivedPaymentController@update');
                Route::post('{id}/discard', 'Api\ReceivedPaymentController@discard');
            });

            Route::prefix('purchases')->group(function () {
                Route::get('records-scroll', 'Api\PurchaseController@byScroll');
                // Route::get('items-scroll', 'Api\PurchaseController@itemsByScroll');
                Route::get('suppliers-scroll', 'Api\PurchaseController@suppliersByScroll');
            });

            Route::prefix('categories')->group(function () {});

            Route::prefix('establishments')->group(function () {
                Route::get('series', 'Api\EstablishmentController@withSeries');
                Route::post('change-user', '\App\Http\Controllers\Tenant\EstablishmentController@changeUserEstablishment');
            });

            Route::prefix('cash')->group(function () {
                Route::get('records-scroll', 'Api\CashController@byScroll');
            });

            Route::prefix('reports')->group(function () {});

        });
    });
}
else
{
    // Dominio system (central): autoregistro de tenant desde la app.
    // La creacion puede tardar varios minutos; no agregar throttle restrictivo.
    Route::domain(env('APP_URL_BASE'))->group(function () {

        Route::middleware('auth:system_api')->prefix('guest-register')->group(function () {
            Route::get('/', 'Api\GuestRegisterController@init');
            Route::get('service/ruc/{number}', '\App\Http\Controllers\System\ServiceController@ruc');
            Route::post('/', 'Api\GuestRegisterController@register');
        });
    });
}
