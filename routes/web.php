<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false]);

Route::group([
    'middleware' => ['auth'],
], function () {
    Route::get('/', 'HomeController@index');
    Route::get('/users', 'HomeController@index');
    Route::get('/translate', 'HomeController@index');

    Route::get('/logout', 'Auth\\LoginController@logout');

    Route::get('/web/users', 'UsersController@index');
});
