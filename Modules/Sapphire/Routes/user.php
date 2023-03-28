<?php

Route::view('login', 'sapphire::login')->name('user-login');
Route::post('login', 'ModuleController@postLogin');

Route::group(['middleware' => 'auth'], function(){
    Route::get('logout', 'ModuleController@getLogout');
});