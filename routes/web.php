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

Route::get('/', 'HomeController@index')->name('home');

Route::group(['prefix' => 'game'], function () {
    Route::get('/', 'GameController@index')->name('list-games');
    Route::get('/create', 'GameController@create')->name('create-game');
    Route::get('/{game}', 'GameController@view')->name('view-game');

    Route::post('/', 'GameController@store')->name('store-game');
    Route::post('/{game}', 'GameController@storePoints')->name('store-points');
    Route::post('/{game}/state', 'GameController@storeState')->name('store-state');
    Route::post('/{game}/round', 'GameController@storeRound')->name('store-round');
});

Route::group(['prefix' => 'user'], function () {
    Route::get('/', 'UserController@profile')->name('profile');
    Route::get('/edit', 'UserController@edit')->name('edit-profile');
    Route::get('/find', 'UserController@find')->name('find-user');
    Route::get('/{user}', 'UserController@show')->name('show-user');

    Route::put('/', 'UserController@store')->name('store-user');
});

Route::group(['prefix' => 'admin'], function () {
    Route::get('/modes', 'AdminController@modes')->name('list-modes');

    Route::post('/modes', 'AdminController@storeMode')->name('store-mode');

    Route::delete('/modes', 'AdminController@deleteMode')->name('delete-mode');
});

Route::group(['prefix' => 'friends'], function () {
    Route::get('/', 'FriendController@index')->name('list-friends');

    Route::post('/', 'FriendController@add')->name('add-friend');

    Route::delete('/', 'FriendController@remove')->name('remove-friend');
});

