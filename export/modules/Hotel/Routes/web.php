<?php

use Illuminate\Support\Facades\Route;

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if ($hostname) {
  Route::domain($hostname->fqdn)->group(function () {
    Route::middleware(['auth', 'redirect.module', 'locked.tenant','check.email.verified'])
      ->prefix('hotels')
      ->group(function () {
        // Tarifas
        Route::get('rates', 'HotelRateController@index');
        Route::post('rates/store', 'HotelRateController@store');
        Route::put('rates/{id}/update', 'HotelRateController@update');
        Route::delete('rates/{id}/delete', 'HotelRateController@destroy');
        // Categorías
        Route::get('categories', 'HotelCategoryController@index');
        Route::post('categories/store', 'HotelCategoryController@store');
        Route::put('categories/{id}/update', 'HotelCategoryController@update');
        Route::delete('categories/{id}/delete', 'HotelCategoryController@destroy');
        // Pisos
        Route::get('floors', 'HotelFloorController@index');
        Route::post('floors/store', 'HotelFloorController@store');
        Route::put('floors/{id}/update', 'HotelFloorController@update');
        Route::delete('floors/{id}/delete', 'HotelFloorController@destroy');
        // Habitaciones
        Route::get('rooms', 'HotelRoomController@index');
        Route::post('rooms/store', 'HotelRoomController@store');
        Route::put('rooms/{id}/update', 'HotelRoomController@update');
        Route::delete('rooms/{id}/delete', 'HotelRoomController@destroy');
        Route::post('rooms/{id}/change-status', 'HotelRoomController@changeRoomStatus');
        Route::delete('rooms/{id}/delete-record', 'HotelRoomController@deleteRecord');

        Route::get('rooms/tables/{id}', 'HotelRoomController@tables');

        Route::get('rooms/{id}/rates', 'HotelRoomController@myRates');
        Route::post('rooms/{id}/rates/store', 'HotelRoomController@addRateToRoom');
        Route::delete('rooms/{id}/rates/{rateId}/delete', 'HotelRoomController@deleteRoomRate');

        Route::prefix('reception')->group(function () {
            /**
            hotels/reception
            hotels/reception/search/
            hotels/reception/tables
            hotels/reception/tables/customers
            hotels/reception/{roomId}/rent
            hotels/reception/{roomId}/rent/store
            hotels/reception/{id}/rent/products
            hotels/reception/{id}/rent/products/store
            hotels/reception/{id}/rent/checkout
            hotels/reception/{id}/rent/finalized
            hotels/reception/{id}/rent/extend-stay
            hotels/reception/{id}/rent/save-payment
            hotels/reception/{id}/rent/extend-time
            hotels/reception/{id}/rent/get-item
            hotels/reception/{id}/rent/observations
              */
            Route::get('', 'HotelReceptionController@index')->name('tenant.hotels.index');
            Route::post('/search', 'HotelReceptionController@searchRooms');
            Route::get('/tables', 'HotelRentController@tables');
            Route::get('/tables/customers', 'HotelRentController@searchCustomers');
            Route::get('/{roomId}/rent', 'HotelRentController@rent');
            Route::post('/{roomId}/rent/store', 'HotelRentController@store');
            Route::get('/{id}/rent/products', 'HotelRentController@showFormAddProduct');
            Route::post('/{id}/rent/products/store', 'HotelRentController@addProductsToRoom');
            Route::get('/{id}/rent/checkout', 'HotelRentController@showFormChekout');
            Route::get('/{id}/rent/checkout-data', 'HotelRentController@getCheckoutData');
            Route::post('/{id}/rent/finalized', 'HotelRentController@finalizeRent');
            Route::post('/{id}/rent/mark-items-invoiced', 'HotelRentController@markItemsInvoiced');
            Route::get('/{id}/rent/invoices-history', 'HotelRentController@invoicesHistory');
            Route::post('/{id}/rent/extend-stay', 'HotelRentController@extendStay');
            Route::post('/{id}/rent/save-payment', 'HotelRentController@savePayment');
            Route::post('/{id}/reverse-payment', 'HotelRentController@reversePayment');
            Route::post('/{id}/rent/extend-time', 'HotelRentController@extendTime');
            Route::get('/{id}/rent/get-item', 'HotelReceptionController@getItem');
            Route::put('/{id}/observations', 'HotelRentController@updateObservations');
            Route::get('checkout-tables', 'HotelRentController@checkoutTables');
            Route::get('rent-products-tables', 'HotelRentController@rentProductsTables');
            Route::get('report/{start}/{end}/{establishment_id}', 'HotelRentController@report');
            Route::post('change-user-establishment', 'HotelReceptionController@changeUserEstablishment');
            Route::get('cleaners', 'HotelCleaningController@getCleaners');
            Route::post('start-cleaning', 'HotelCleaningController@startQuickCleaning');
            Route::post('assign-cleaner-start', 'HotelCleaningController@assignCleanerAndStart');
            Route::post('complete-cleaning/{id}', 'HotelCleaningController@completeCleaning');
            Route::get('active-cleanings', 'HotelCleaningController@getActiveCleanings');
            Route::get('room-cleaning-history/{roomId}', 'HotelCleaningController@getRoomCleaningHistory');
            Route::get('cleaner-assignments/{cleanerId}', 'HotelCleaningController@getCleanerAssignments');
            Route::get('available-rooms', 'HotelReceptionController@getAvailableRooms');
            Route::post('{id}/change-room', 'HotelRentController@changeRoom');
            Route::post('{id}/edit-dates', 'HotelReceptionController@editDates');
            Route::get('{id}/room-history', 'HotelReceptionController@getRoomHistory');
            Route::get('rooms/{id}', 'HotelReceptionController@getRoom');
            Route::post('{id}/record-change', 'HotelReceptionController@recordChangePublic');

        });

        // Calendario de Reservas
        Route::prefix('reservations')->group(function () {
            Route::get('calendar', 'HotelReservationCalendarController@index')->name('tenant.hotels.calendar');
            Route::get('calendar/events', 'HotelReservationCalendarController@getCalendarEvents');
            Route::get('calendar/rooms', 'HotelReservationCalendarController@getRooms');
            Route::get('calendar/{id}/details', 'HotelReservationCalendarController@getReservationDetails');
            Route::put('calendar/{id}/status', 'HotelReservationCalendarController@updateReservationStatus');
            Route::get('calendar/range', 'HotelReservationCalendarController@getReservationsByDateRange');
            // CRUD de reservas desde el calendario
            Route::get('calendar/form-tables', 'HotelReservationCalendarController@getFormTables');
            Route::get('calendar/search-customers', 'HotelReservationCalendarController@searchCustomers');
            Route::get('calendar/room/{roomId}', 'HotelReservationCalendarController@getRoomForForm');
            Route::post('calendar/store', 'HotelReservationCalendarController@storeReservation');
            Route::put('calendar/{id}/update', 'HotelReservationCalendarController@updateReservation');
            Route::delete('calendar/{id}/delete', 'HotelReservationCalendarController@deleteReservation');
            Route::get('calendar/daily-sales-total', 'HotelReservationCalendarController@getDailySalesTotal');
            Route::get('calendar/category-daily-sales-total', 'HotelReservationCalendarController@getCategoryDailySalesTotal');
        });
    });
  });
}
