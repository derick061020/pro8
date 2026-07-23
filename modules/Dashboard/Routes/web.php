<?php
use Illuminate\Support\Facades\Route;
$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth', 'locked.tenant', 'check.email.verified'])->group(function () {

            Route::redirect('/', '/dashboard');

            Route::prefix('dashboard')->group(function () {
                Route::get('/', 'DashboardController@index')->name('tenant.dashboard.index');
                Route::get('filter', 'DashboardController@filter');
                Route::post('data', 'DashboardController@data');
                Route::get('kpi', 'DashboardController@kpi');
                Route::get('monthly-comparison', 'DashboardController@monthlyComparison');
                Route::get('sales-growth', 'DashboardController@salesGrowth');
                Route::post('data_aditional', 'DashboardController@data_aditional');
                Route::post('igv-sales', 'DashboardController@igvSales');
                Route::post('igv-purchases', 'DashboardController@igvPurchases');
                // Route::post('unpaid', 'DashboardController@unpaid');
                // Route::get('unpaidall', 'DashboardController@unpaidall')->name('unpaidall');
                Route::get('stock-by-product/records', 'DashboardController@stockByProduct');
                Route::get('product-of-due/records', 'DashboardController@productOfDue');
                Route::post('utilities', 'DashboardController@utilities');
                Route::get('global-data', 'DashboardController@globalData');
                Route::get('cash-flow', 'DashboardController@cashFlow');
                Route::get('low-stock', 'DashboardController@lowStock');
                Route::get('sales-week', 'DashboardController@salesWeek');
                Route::get('payment-methods', 'DashboardController@paymentMethods');
                Route::get('sunat-status', 'DashboardController@sunatStatus');
                Route::get('debtors', 'DashboardController@debtors');
                Route::get('month-goal', 'DashboardController@monthGoal');
                Route::get('sales-by-product', 'DashboardController@salesByProduct');
            });

            //Commands
            Route::get('command/df', 'DashboardController@df')->name('command.df');

        });
    });
}
