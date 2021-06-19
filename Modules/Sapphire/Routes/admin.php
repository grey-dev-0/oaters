<?php

Route::view('login', 'sapphire::login')->name('admin-login');
Route::post('login', 'ModuleController@postLogin');

Route::group(['middleware' => ['auth:admin', 'can:manage-tenants']], function(){
    Route::get('logout', 'ModuleController@getLogout');
    Route::view('/', 'sapphire::admin.dashboard');
});
