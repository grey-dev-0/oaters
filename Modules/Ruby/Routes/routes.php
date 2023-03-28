<?php

Route::get('login', 'ModuleController@getLogin')->name('user-login');
Route::post('login', 'ModuleController@postLogin');

Route::group(['middleware' => ['auth', 'authorize:ruby']], function(){
    Route::get('logout', 'ModuleController@getLogout');
    Route::get('/', 'DashboardController@getIndex');
});