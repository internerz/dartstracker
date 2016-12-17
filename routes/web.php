<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();

Route::get('/', 'HomeController@index');

Route::group(['prefix' => 'game'], function () {
    Route::get('/', 'GameController@index');
    Route::get('/create', 'GameController@create');
    Route::get('/{game}', 'GameController@view');

    Route::post('/', 'GameController@store');
    Route::post('/{game}', 'GameController@storePoints');
});

Route::group(['prefix' => 'user'], function () {
    Route::get('/', 'UserController@profile');
    Route::get('/edit', 'UserController@edit');
    Route::get('/user/find', 'UserController@find');
    Route::get('/user/{user}', 'UserController@show');

    Route::put('/', 'UserController@store');
});

Route::group(['prefix' => 'admin'], function () {
    Route::get('/modes', 'AdminController@modes');

    Route::post('/modes', 'AdminController@storeMode');

    Route::delete('/modes', 'AdminController@deleteMode');
});

Route::group(['prefix' => 'friends'], function () {
    Route::get('/', 'FriendController@index');

    Route::post('/', 'FriendController@add');

    Route::delete('/', 'FriendController@remove');
});

