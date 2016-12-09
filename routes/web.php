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
Route::get('/game', 'GameController@index');
Route::get('/game/create', 'GameController@create');
Route::get('/game/{game}', 'GameController@view');
Route::get('/modes', 'AdminController@modes');
Route::get('/user/find', 'UserController@find');
Route::get('/user/{user}', 'UserController@show');
Route::get('/friends', 'FriendController@index');
Route::get('/friend/add/{user}', 'FriendController@add');
Route::get('/friend/remove/{user}', 'FriendController@remove');
Route::get('/profile', 'UserController@profile');
Route::get('/profile/edit', 'UserController@edit');
Route::post('/game', 'GameController@store');
Route::post('/game/{game}', 'GameController@storePoints');
Route::post('/modes', 'AdminController@storeMode');
Route::post('/friends', 'FriendController@store');
Route::put('/profile', 'UserController@store');
Route::delete('/modes', 'AdminController@deleteMode');
Route::delete('/friends', 'FriendController@deleteFriend');


