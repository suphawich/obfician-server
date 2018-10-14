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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/images/posts/cover/{filename}', function($filename) {
    $pathToFile = storage_path().'/app/public/images/posts/cover/'.$filename;
    return response()->file($pathToFile);
});

Route::get('/images/items/{filename}', function($filename) {
    $pathToFile = storage_path().'/app/public/images/items/'.$filename;
    return response()->file($pathToFile);
});