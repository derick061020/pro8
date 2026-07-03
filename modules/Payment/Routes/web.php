<?php

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($hostname) {
    Route::domain($hostname->fqdn)->group(function () {

        
        Route::prefix('pagos')->group(function () {
            Route::get('{uuid}/{payment_link_type_id}/{total}', 'PaymentLinkController@publicPaymentLink');
        });

        Route::middleware(['auth', 'locked.tenant'])->group(function() {

            Route::prefix('payment-configurations')->group(function () {

                Route::post('', 'PaymentConfigurationController@store');
                Route::get('/record', 'PaymentConfigurationController@record');
                Route::get('/record-permissions', 'PaymentConfigurationController@recordPermissions');
                Route::post('upload-qrcode-yape', 'PaymentConfigurationController@uploadQrcodeYape');

            });

            Route::prefix('payment-links')->group(function () {

                Route::get('columns', 'PaymentLinkController@columns');
                Route::get('records', 'PaymentLinkController@records');
                Route::get('tables', 'PaymentLinkController@tables');

                Route::post('', 'PaymentLinkController@store');
                Route::get('record/{document_payment_id}/{instance_type}/{payment_link_type_id}', 'PaymentLinkController@record');

                Route::get('transactions/{id}', 'PaymentLinkController@transactions');
                Route::delete('/{id}', 'PaymentLinkController@destroy');

                Route::post('store-without-payment', 'PaymentLinkController@storeWithoutPayment');
                Route::get('record-without-payment/{id}', 'PaymentLinkController@recordWithoutPayment');
                
                Route::get('', 'PaymentLinkController@index')->name('tenant.payment.generate.index');
                Route::post('email', 'PaymentLinkController@email');
                Route::post('uploaded-file', 'PaymentLinkController@uploadedFile');
                Route::post('query-transaction-state', 'PaymentLinkController@queryTransactionState');

            });

            Route::prefix('payment-gateway')->group(function() {

                Route::get('enabled-checkout', 'PaymentGatewayController@enabledCheckouts')->name('system.enabled.checkouts');

                Route::prefix('culqi')->group(function() {
                    Route::get('record', 'PaymentGatewayController@culqiRecord')->name('tenant.culqi.configuration')->withoutMiddleware(['locked.tenant', 'auth', 'web', 'redirect.level']);
                    Route::post('charge', 'PaymentGatewayController@culqiCreateCharge');
                    Route::post('webhook', 'PaymentGatewayController@webhook');
                });

                Route::prefix('izipay')->group(function() {
                    Route::get('record', 'PaymentGatewayController@izipayRecord')->name('tenant.izipay.record');
                    Route::post('payment', 'PaymentGatewayController@izipayCreatePayment');
                    Route::post('transaction', 'PaymentGatewayController@izipayTransaction')->name('tenant.izipay.transaction');
                });
            });


        });
    });
} else {
        Route::middleware('auth:admin')->group(function () {
            Route::prefix('payment-gateway')->group(function() {

                Route::get('enabled-checkout', 'PaymentGatewayController@enabledCheckouts')->name('system.enabled.checkouts');

                Route::prefix('culqi')->group(function() {
                    Route::get('record', 'PaymentGatewayController@culqiRecord')->name('system.culqi.configuration');
                    Route::post('charge', 'PaymentGatewayController@culqiCreateCharge');
                    // Route::post('result', 'PaymentGatewayController@culqiResult')->name('system.culqi.result');
                });

                Route::prefix('izipay')->group(function() {
                    Route::get('record', 'PaymentGatewayController@izipayRecord')->name('system.izipay.record');
                    Route::post('payment', 'PaymentGatewayController@izipayCreatePayment');
                    Route::post('transaction', 'PaymentGatewayController@izipayTransaction')->name('tenant.izipay.transaction');
                });
            });
        });

}
