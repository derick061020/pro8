<?php

use Illuminate\Support\Facades\Route;

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if ($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth', 'locked.tenant'])->group(function () {
            Route::prefix('configurations/custom-fields')->group(function () {
                Route::get('/', 'CustomFieldController@index')->name('tenant.custom-fields.index');
                Route::post('columns', 'CustomFieldController@columns');
                Route::post('records', 'CustomFieldController@records');
                Route::get('record/{id}', 'CustomFieldController@record');
                Route::post('store', 'CustomFieldController@store');
                Route::delete('destroy/{id}', 'CustomFieldController@destroy');
                Route::post('update-document-status', 'CustomFieldController@updateDocumentStatus');
            });
        });
    });
}