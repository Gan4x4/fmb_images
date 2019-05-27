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

Route::middleware('auth.admin')->get('images/suspicious', 'ImageController@suspicious')->name('images.suspicious');

Route::middleware('auth')->get('images/new/first', 'ImageController@editFirstNewImage')->name('images.new.first');
Route::middleware('auth')->post('images/{id}/take', 'ImageController@take')->name('images.take');
Route::middleware('auth')->get('images/{id}/exists', 'ImageController@alreadyExists')->name('images.exists');
Route::middleware('auth')->post('images/load_complaints', 'ImageController@loadComplaints')->name('images.load.complaints');


Route::middleware('auth')->delete('images/{id}/ajax', 'ImageController@destroyAjax')->name('images.destroy.ajax');



Route::middleware('auth')->resource('images', 'ImageController');

Route::middleware('auth.admin')->resource('properties', 'PropertyController');
Route::middleware('auth.admin')->resource('tags', 'TagController');


Route::middleware('auth.admin')->resource('items', 'ItemController');

Route::middleware('auth.admin')->resource('users', 'UserController');

//Route::middleware('auth.admin')->get('items/build', '@build')->name('build');
Route::middleware('auth.admin')->resource('builds', 'BuildController');


Route::get('test','TestController@index');
Route::get('bike2frame','TestController@bike2frame');
Route::get('frame','TestController@withoutFrame');
Route::get('new_prop','TestController@new_prop');
Route::get('im_count','TestController@getImagesCount');