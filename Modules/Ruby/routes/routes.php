<?php

Route::get('login', 'ModuleController@getLogin')->name('login');
Route::post('login', 'ModuleController@postLogin');

Route::group(['middleware' => ['auth', 'authorize:ruby']], function(){
    Route::get('/', 'DashboardController@getIndex');
});