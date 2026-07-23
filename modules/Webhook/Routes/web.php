<?php

use Illuminate\Support\Facades\Route;

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if ($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth', 'locked.tenant'])->group(function () {
            Route::prefix('configurations/webhooks')->group(function () {
                Route::get('/', 'WebhookSubscriptionController@index')->name('tenant.webhooks.index');
                Route::get('events', 'WebhookSubscriptionController@events');
                Route::post('records', 'WebhookSubscriptionController@records');
                Route::get('record/{id}', 'WebhookSubscriptionController@record');
                Route::post('store', 'WebhookSubscriptionController@store');
                Route::post('toggle/{id}', 'WebhookSubscriptionController@toggle');
                Route::delete('destroy/{id}', 'WebhookSubscriptionController@destroy');

                Route::post('deliveries/records', 'WebhookDeliveryController@records');
                Route::post('deliveries/{id}/resend', 'WebhookDeliveryController@resend');
            });
        });
    });
}
