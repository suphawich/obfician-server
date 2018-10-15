<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::resource('/posts', 'API\PostsController')
         ->except(['create', 'edit']);

Route::resource('/items', 'API\ItemsController')
         ->except(['create', 'edit']);

Route::resource('/events', 'API\EventsController')
         ->except(['create', 'edit']);

Route::resource('/users', 'API\UsersController')
         ->except(['create', 'edit']);
Route::put('/users/{id}/changepwd', 'API\UsersController@updatePwd');
Route::put('/users/{id}/verifyEmail', 'API\UsersController@verifyEmail');
Route::put('/users/{id}/changeavatar', 'API\UsersController@updateAvatar');

// Route::put('/hello/{id}', function(Request $request, $name) {
//     error_log($name);
//     return $request->input('date');
// });