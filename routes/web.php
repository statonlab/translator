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
    Route::get('/languages', 'HomeController@index');
    Route::get('/profile', 'HomeController@index');

    // Logout
    Route::get('/logout', 'Auth\\LoginController@logout');

    /*
     * API Endpoints
     * -------------
     *
     * The following URLs point to web api endpoints.
     */

    // Users
    Route::get('/web/users', 'UsersController@index');
    Route::post('/web/users', 'UsersController@create');
    Route::delete('/web/users/{user}', 'UsersController@delete');
    Route::patch('/web/users/{user}', 'UsersController@patch');

    // Languages
    Route::get('/web/languages', 'LanguagesController@index');
    Route::post('/web/languages', 'LanguagesController@create');
    Route::delete('/web/languages/{language}', 'LanguagesController@delete');
    Route::patch('/web/languages/{language}', 'LanguagesController@patch');
});
