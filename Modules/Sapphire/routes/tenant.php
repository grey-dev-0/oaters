<?php

Route::view('login', 'sapphire::login')->name('tenant-login');
Route::post('login', 'ModuleController@postLogin');