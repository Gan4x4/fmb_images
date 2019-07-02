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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
Route::get('/feature/{feature_id}/item/{item_id}/properties/', 'ItemController@properties')->name('item.properties');
Route::get('/features/{feature_id}', 'ImageController@feature')->name('feature.properties');
*/

Route::get('/items/{item_id}/properties/', 'ItemController@properties')->name('item.properties');
Route::delete('/images/{id}/features/all', 'FeatureController@destroyAll')->name('images.features.delete.all');
Route::resource('images.features', 'FeatureController');