<?php

Route::view('login', 'sapphire::login')->name('admin-login');
Route::post('login', 'ModuleController@postLogin');

Route::group(['middleware' => ['auth:admin', 'can:manage-tenants']], function(){
    Route::get('logout', 'ModuleController@getLogout');
    Route::view('tenants', 'sapphire::admin.tenants');
    Route::post('tenants', 'TenantController@postIndex');
    Route::view('payments', 'sapphire::admin.payments');
    Route::post('payments', 'PurchaseController@postIndex');
    Route::group(['prefix' => 'subscriptions'], function(){
        Route::get('{subscription}/modules', 'TenantController@getModules');
    });
    Route::group(['prefix' => 'charts'], function(){
        Route::post('subscriptions-pie', 'DashboardController@postSubscriptionsPieChart');
        Route::post('subscriptions-line', 'DashboardController@postSubscriptionsLineChart');
        Route::post('payments-line', 'DashboardController@postPurchasesLineChart');
    });
    Route::view('/', 'sapphire::admin.dashboard');
});
