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
    // Vue routes
    Route::get('/', 'HomeController@index');
    Route::get('/users', 'HomeController@index');
    Route::get('/translate', 'HomeController@index');
    Route::get('/languages', 'HomeController@index');
    Route::get('/profile', 'HomeController@index');
    Route::get('/platforms', 'HomeController@index');
    Route::get('/platform/{platform}/files', 'HomeController@index');

    // Logout route
    Route::get('/logout', 'Auth\\LoginController@logout');

    /*
     * API Endpoints
     * -------------
     *
     * The following URLs point to web api endpoints.
     */

    // User routes
    Route::get('/web/users', 'UsersController@index');
    Route::post('/web/users', 'UsersController@create');
    Route::delete('/web/user/{user}', 'UsersController@delete');
    Route::get('/web/user/{user?}', 'UsersController@show');
    Route::patch('/web/user/password', 'UsersController@patchPassword');
    Route::patch('/web/user/{user?}', 'UsersController@patch');
    Route::put('/web/user/{user?}', 'UsersController@update');

    // Translation routes
    Route::get('/web/translation/languages', 'TranslationsController@languages');
    Route::get('/web/translation/lines/{platform}', 'TranslationsController@lines');
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

    // Files
    Route::get('/web/files', 'FilesController@index');
    Route::get('/web/file/{file}', 'FilesController@show');
    Route::post('/web/files', 'FilesController@create');
    Route::put('/web/file/{file}', 'FilesController@update');
    Route::delete('/web/file/{file}', 'FilesController@delete');

    // Platform files
    Route::get('/web/platform/{platform}/files', 'PlatformsController@files');

    // Downloads
    Route::get('/download/file/{file}', 'FilesController@download');
});
