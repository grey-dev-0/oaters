<?php

Route::get('login', 'ModuleController@getLogin')->name('login');
Route::post('login', 'ModuleController@postLogin');

Route::group(['middleware' => ['auth', 'can:ruby']], function(){
    Route::get('logout', 'ModuleController@getLogout')->name('logout');

    Route::prefix('departments')->as('departments.')->group(function(){
        Route::view('/', 'ruby::departments')->name('index');
        Route::post('/', 'DepartmentController@postIndex');
    });

    Route::get('/', 'DashboardController@getIndex');
});