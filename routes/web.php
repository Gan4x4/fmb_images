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
})->name('welcome');

Route::get('/home', function () {
    return redirect()->route('images.index');
})->name('home');

Auth::routes();  // Registration inside


Route::middleware('auth')->post('images/{id}/take', 'ImageController@take')->name('images.take');
Route::middleware('auth')->get('images/{id}/exists', 'ImageController@alreadyExists')->name('images.exists');
Route::middleware('auth')->resource('images', 'ImageController');

Route::middleware('auth.admin')->resource('properties', 'PropertyController');
Route::middleware('auth.admin')->resource('tags', 'TagController');

Route::middleware('auth.admin')->get('items/build', 'ItemController@build')->name('build');
Route::middleware('auth.admin')->resource('items', 'ItemController');

Route::get('test','TestController@index');