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

Route::get('messages/{thread}', 'InstagramController@getThread');
Route::post('messages/{thread}', 'InstagramController@replyToThread');
Route::post('sync-product', 'ScrapController@syncGnbProducts');
Route::post('scrap-products/add', 'ScrapController@syncProductsFromNodeApp');
Route::post('add-product-entries', 'ScrapController@addProductEntries');
Route::post('add-product-images', 'ScrapController@getProductsForImages');
Route::post('save-product-images', 'ScrapController@saveImagesToProducts');
Route::post('save-supplier', 'ScrapController@saveSupplier');
Route::get('hashtags', 'HashtagController@sendHashtagsApi');
Route::get('crop', 'ProductController@giveImage');