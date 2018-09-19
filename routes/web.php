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

/* Images */
//Route::put('images/{image_id}/features/', 'ImageController@updateOrCreateFeature')->name('images.features.update_or_create');
//Route::delete('images/{image_id}/features/{id}', 'ImageController@deleteFeature')->name('images.features.delete');
Route::resource('images', 'ImageController');

Route::resource('properties', 'PropertyController');
Route::resource('tags', 'TagController');

Route::get('items/build', 'ItemController@build')->name('build');
Route::resource('items', 'ItemController');

Route::get('test','TestController@index');