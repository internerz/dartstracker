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
Route::post('/game', 'GameController@store');
Route::post('/game/{game}', 'GameController@storePoints');
Route::post('/modes', 'AdminController@storeMode');
Route::delete('/modes', 'AdminController@deleteMode');


