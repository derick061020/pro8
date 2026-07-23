<?php

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname)
{
    Route::domain($current_hostname->fqdn)->group(function () {

        Route::middleware(['auth','redirect.module', 'locked.tenant','check.email.verified'])->group(function () {

            Route::get('/live-app', 'LiveAppController@index')->name('tenant.liveapp.index');

            Route::prefix('app')->group(function() {

                Route::get('/configuration', 'AppConfigurationController@view')->name('tenant.liveapp.configuration');

                Route::prefix('configurations')->group(function () {
                    Route::get('', 'AppConfigurationController@record');
                    Route::post('', 'AppConfigurationController@store');
                });

                Route::prefix('permissions')->group(function () {
                    Route::get('record/{user_id}', 'AppPermissionController@record');
                    Route::get('tables', 'AppPermissionController@tables');
                    Route::post('', 'AppPermissionController@store');
                });

            });

        });

        Route::prefix('report-app')->group(function() {
            Route::get('pdf-general-sale', 'LiveAppController@pdfReportGeneralSale');
        });
    });

}