<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['check.permission', 'locked.tenant','check.email.verified'])->prefix('ecommerce')->group(function() {
   // Route::get('/', 'EcommerceController@index');


    Route::get('/', 'EcommerceController@index')->name('tenant.ecommerce.index');

    Route::get('item/{id}/{slug?}', 'EcommerceController@item')->name('tenant.ecommerce.item');

    Route::get('items', 'EcommerceController@items')->name('tenant.ecommerce.item.index');
    Route::get('item_partial/{id}', 'EcommerceController@partialItem')->name('item_partial');
    Route::get('detail_cart', 'EcommerceController@detailCart')->name('tenant_detail_cart');
    Route::get('document_list', 'EcommerceController@documentList')->name('tenant_document_list');
    Route::get('documents', 'EcommerceController@documents')->name('tenant_document');
    Route::get('orders', 'EcommerceController@orders')->name('tenant_orders');

    Route::get('order_list', 'EcommerceController@orderList')->name('tenant_order_list');
    Route::get('account', 'EcommerceController@account')->name('tenant_ecommerce_account');
    Route::get('pay_cart', 'EcommerceController@pay')->name('tenant_pay_cart');
    Route::get('login', 'EcommerceController@showLogin')->name('tenant_ecommerce_login');
    Route::post('logout', 'EcommerceController@logout')->name('tenant_ecommerce_logout');
    Route::get('items_bar', 'EcommerceController@itemsBar');
    Route::post('login', 'EcommerceController@login');
    Route::post('storeUser', 'EcommerceController@storeUser')->name('tenant_ecommerce_store_user');
    Route::get('search-document/{number}', 'EcommerceController@searchDocumentPublic')->name('tenant_ecommerce_search_document');
    Route::post('rating_item', 'EcommerceController@ratingItem')->name('tenant_ecommerce_rating_item');
    Route::get('rating_item/{id}', 'EcommerceController@getRating');
    Route::get('color-ecommerce', 'ConfigurationController@getColorEcommerce');
    Route::get('google-maps', 'EcommerceController@getGoogleMaps');
    Route::get('google-maps-script', 'EcommerceController@getGoogleMapsScript')->name('google_maps_script');
    Route::get('get-location-cascade', 'EcommerceController@getLocationCascade')->name('get_location_cascade');

    Route::post('culqi', 'CulqiController@payment')->name('tenant_ecommerce_culqui');
    Route::post('transaction_finally', 'EcommerceController@transactionFinally')->name('tenant_ecommerce_transaction_finally');
    Route::post('payment_cash', 'EcommerceController@paymentCash')->name('tenant_ecommerce_payment_cash');
    Route::post('validate-coupon', 'EcommerceController@validateCoupon')->name('tenant_ecommerce_validate_coupon');
    Route::post('apply-coupon', 'EcommerceController@applyCoupon')->name('tenant_ecommerce_apply_coupon');


    // Página de términos y condiciones
    Route::get('terminos-y-condiciones', 'EcommerceController@termsConditions')->name('tenant_ecommerce_terms_conditions');
    // Página de política de privacidad
    Route::get('politica-de-privacidad', 'EcommerceController@privacyPolicy')->name('tenant_ecommerce_privacy_policy');
    // Página de sobre nosotros
    Route::get('nosotros', 'EcommerceController@aboutUs')->name('tenant_ecommerce_about_us');
    // Página de gracias tras completar el pago
    Route::get('thanks/{external_id}', 'EcommerceController@thankYou')->name('tenant_ecommerce_thank_you');

    Route::get('configuration', 'ConfigurationController@index')->middleware(['auth', 'redirect.module'])->name('tenant_ecommerce_configuration');
    Route::post('configuration', 'ConfigurationController@store_configuration');
    Route::post('configuration_delivery', 'ConfigurationController@store_configuration_delivery');
    Route::post('configuration_culqui', 'ConfigurationController@store_configuration_culqui');
    Route::post('configuration_paypal', 'ConfigurationController@store_configuration_paypal');
    Route::post('configuration_social', 'ConfigurationController@store_configuration_social');
    Route::post('configuration_tags', 'ConfigurationController@store_configuration_tag');
    Route::post('configuration_color', 'ConfigurationController@store_configuration_color');
    Route::post('saveDataUser', 'EcommerceController@saveDataUser')->name('tenant_ecommerce_user_data');
    Route::post('configuration_links', 'ConfigurationController@store_configuration_links');

    Route::get('record', 'ConfigurationController@record');

    Route::post('uploads', 'ConfigurationController@uploadFile');

    //Item Sets
    // Cupones de descuento
    Route::prefix('discount-coupons')->group(function () {
        Route::get('/', 'DiscountCouponController@index')->name('tenant.ecommerce.discount_coupons.index')->middleware('redirect.level');
        Route::get('/tables', 'DiscountCouponController@tables');
        Route::post('/records', 'DiscountCouponController@records');
        Route::get('/record', 'DiscountCouponController@record');
        Route::post('/', 'DiscountCouponController@store');
        Route::post('/{id}/status', 'DiscountCouponController@updateStatus');
        Route::delete('/{id}', 'DiscountCouponController@destroy');
    });

    // Zonas de delivery
    Route::get('delivery-zones/check', 'EcommerceController@checkDeliveryZone')->name('tenant.ecommerce.delivery_zones.check');
    Route::prefix('delivery-zones')->group(function () {
        Route::post('/records', 'DeliveryZoneController@records');
        Route::get('/record', 'DeliveryZoneController@record');
        Route::post('/', 'DeliveryZoneController@store');
        Route::post('/{id}/status', 'DeliveryZoneController@updateStatus');
        Route::delete('/{id}', 'DeliveryZoneController@destroy');
    });

    // Sucursales de recojo en tienda
    Route::prefix('pickup-branches')->group(function () {
        Route::get('/records', 'PickupBranchController@records');
        Route::get('/public', 'PickupBranchController@publicRecords');
        Route::post('/', 'PickupBranchController@store');
        Route::post('/sync', 'PickupBranchController@sync');
        Route::post('/{id}/status', 'PickupBranchController@updateStatus');
        Route::delete('/{id}', 'PickupBranchController@destroy');
    });

    Route::prefix('item-sets')->group(function() {

        Route::get('', 'ItemSetController@index')->name('tenant.ecommerce.item_sets.index')->middleware('redirect.level');
        Route::get('columns', 'ItemSetController@columns');
        Route::get('records', 'ItemSetController@records');
        Route::get('tables', 'ItemSetController@tables');
        Route::get('record/{item}', 'ItemSetController@record');
        Route::post('', 'ItemSetController@store');
        Route::delete('{item}', 'ItemSetController@destroy');
        Route::delete('item-unit-type/{item}', 'ItemSetController@destroyItemUnitType');
        Route::post('import', 'ItemSetController@import');
        Route::post('upload', 'ItemSetController@upload');
        Route::post('visible_store', 'ItemSetController@visibleStore');
        Route::get('item/tables', 'ItemSetController@item_tables');

    });

    Route::get('sitemap.xml', 'EcommerceController@sitemap')->name('tenant.ecommerce.sitemap');

    // Ruta de categoría al final para no interferir con rutas específicas
    Route::get('/{category}', 'EcommerceController@index')->name('tenant.ecommerce.category');

});


Route::middleware(['locked.tenant'])->group(function() {
    // ecommerce
    Route::get('/ecommerce/{name?}', 'EcommerceController@index');

    // Libro de Reclamaciones embebido en el layout del ecommerce
    Route::get('/libro-de-reclamaciones', 'EcommerceController@claimsBook')->name('tenant.ecommerce.claims_book');
});
