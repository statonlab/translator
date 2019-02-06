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

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
| Accessible only to authenticated users despite their role in
| the application. That is, users with role "Admin" and users
| with role "User" can access these routes. Fine grained
| control of each method can be done using Policies.
*/
Route::group([
    'middleware' => ['auth'],
], function () {
    Route::get('/', 'HomeController@index');
    Route::get('/users', 'HomeController@index');
    Route::get('/translate', 'HomeController@index');
    Route::get('/languages', 'HomeController@index');
    Route::get('/profile', 'HomeController@index');
    Route::get('/platforms', 'HomeController@index');

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
    Route::delete('/web/user/{user}', 'UsersController@delete');
    Route::get('/web/user/{user?}', 'UsersController@show');
    Route::patch('/web/user/password', 'UsersController@patchPassword');
    Route::patch('/web/user/{user?}', 'UsersController@patch');
    Route::put('/web/user/{user?}', 'UsersController@update');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Accessible only to users who have the role Admin.
*/
Route::group([
    'middleware' => ['auth', 'admin'],
], function () {
    // Languages
    Route::get('/web/languages', 'LanguagesController@index');
    Route::post('/web/languages', 'LanguagesController@create');
    Route::delete('/web/language/{language}', 'LanguagesController@delete');
    Route::patch('/web/language/{language}', 'LanguagesController@patch');
    Route::post('/web/language/{language}/user', 'LanguagesController@toggleAssignment');
    Route::get('/web/language/{language}/users', 'LanguagesController@users');

    // Platforms
    Route::get('/web/platforms', 'PlatformsController@index');
    Route::post('/web/platforms', 'PlatformsController@create');
    Route::get('/web/platform/{platform}', 'PlatformsController@show');
    Route::put('/web/platform/{platform}', 'PlatformsController@update');
    Route::delete('/web/platform/{platform}', 'PlatformsController@delete');
    Route::patch('/web/platform/{platform}', 'PlatformsController@patch');
});
