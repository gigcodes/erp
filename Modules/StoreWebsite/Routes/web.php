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

Route::prefix('store-website')->group(function () {
    Route::get('/', 'StoreWebsiteController@index')->name("store-website.index");
    Route::get('/records', 'StoreWebsiteController@records')->name("store-website.records");
    Route::post('/save', 'StoreWebsiteController@save')->name("store-website.save");
    Route::prefix('{id}')->group(function () {
        Route::get('/edit', 'StoreWebsiteController@edit')->name("store-website.edit");
        Route::get('/delete', 'StoreWebsiteController@delete')->name("store-website.delete");
    	
        Route::prefix('attached-category')->group(function () {
    		Route::get('/', 'CategoryController@index')->name("store-website.attached-category.index");
    		Route::post('/', 'CategoryController@store')->name("store-website.attached-category.store");
            Route::prefix('{store_category_id}')->group(function () {
                Route::get('/delete', 'CategoryController@delete')->name("store-website.attached-category.delete");
            });    
    	});		

        Route::prefix('attached-brand')->group(function () {
            Route::get('/', 'BrandController@index')->name("store-website.attached-brand.index");
            Route::post('/', 'BrandController@store')->name("store-website.attached-brand.store");
            Route::prefix('{store_brand_id}')->group(function () {
                Route::get('/delete', 'BrandController@delete')->name("store-website.attached-brand.delete");
            });    
        });


        

    });    
});
