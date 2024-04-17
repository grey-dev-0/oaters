<?php

Route::view('login', 'sapphire::login')->name('login');
Route::post('login', 'ModuleController@postLogin');

Route::group(['middleware' => ['auth:admin', 'role:master']], function(){
    Route::get('logout', 'ModuleController@getLogout')->name('logout');
    Route::view('tenants', 'sapphire::admin.tenants')->name('tenants');
    Route::post('tenants', 'TenantController@postIndex');
    Route::get('subscriptions', 'TenantController@getSubscriptions')->name('subscriptions');
    Route::post('subscriptions', 'TenantController@postSubscriptions');
    Route::view('payments', 'sapphire::admin.payments')->name('payments');
    Route::post('payments', 'PurchaseController@postIndex');
    Route::group(['prefix' => 'subscriptions'], function(){
        Route::get('{subscription}/modules', 'TenantController@getModules');
    });
    Route::group(['prefix' => 'charts'], function(){
        Route::post('subscriptions-pie', 'DashboardController@postSubscriptionsPieChart');
        Route::post('subscriptions-line', 'DashboardController@postSubscriptionsLineChart');
        Route::post('payments-line', 'DashboardController@postPurchasesLineChart');
    });
    Route::post('autocomplete/tenants', 'TenantController@postAutocomplete');
    Route::get('/', 'DashboardController@getIndex');
});
