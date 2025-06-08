<?php

Route::get('login', 'ModuleController@getLogin')->name('login');
Route::post('login', 'ModuleController@postLogin');

Route::group(['middleware' => ['auth', 'can:ruby']], function(){
    Route::get('logout', 'ModuleController@getLogout')->name('logout');

    Route::prefix('departments')->as('departments.')->group(function(){
        Route::get('/', 'DepartmentController@getIndex')->name('index');
        Route::post('/', 'DepartmentController@postIndex');
        Route::post('create', 'DepartmentController@postCreateOrUpdate')->name('create');
        Route::post('update', 'DepartmentController@postCreateOrUpdate')->name('update');
        Route::get('{department}', 'DepartmentController@getDepartment')->where('department', '[0-9]+')->name('department');
    });

    Route::prefix('contacts')->as('contacts.')->group(function(){
        Route::get('/', 'ContactController@getIndex')->name('index');
        Route::post('/', 'ContactController@postIndex');
        Route::get('{contact}', 'ContactController@getContact')->where('contact', '[0-9]+')->name('contact');
        Route::post('search', 'ContactController@postSearch')->name('search');
        Route::get('structure', 'ContactController@getStructure')->name('structure');
    });

    Route::prefix('documents')->as('documents.')->group(function(){
        Route::get('download/{filename}', 'DocumentController@getDocument')->name('download');
    });

    Route::get('/', 'DashboardController@getIndex');
});