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
        Route::get('/child-categories', 'CategoryController@getChildCategories')->name("store-website.child-categories");
        Route::post('/submit-social-remarks', 'StoreWebsiteController@updateSocialRemarks')->name("store-website.update.social-remarks");
    	
        Route::prefix('attached-category')->group(function () {
    		Route::get('/', 'CategoryController@index')->name("store-website.attached-category.index");
    		Route::post('/', 'CategoryController@store')->name("store-website.attached-category.store");
            Route::prefix('{store_category_id}')->group(function () {
                Route::get('/delete', 'CategoryController@delete')->name("store-website.attached-category.delete");
            });    
    	});

        Route::prefix('attached-categories')->group(function () {
            Route::post('/', 'CategoryController@storeMultipleCategories')->name("store-website.attached-categories.store");
        }); 
         
        Route::prefix('attached-brand')->group(function () {
            Route::get('/', 'BrandController@index')->name("store-website.attached-brand.index");
            Route::post('/', 'BrandController@store')->name("store-website.attached-brand.store");
            Route::prefix('{store_brand_id}')->group(function () {
                Route::get('/delete', 'BrandController@delete')->name("store-website.attached-brand.delete");
            });    
        });

        Route::prefix('goal')->group(function () {
            Route::get('/', 'GoalController@index')->name("store-website.goal.index");
            Route::get('records', 'GoalController@records')->name("store-website.goal.records");
            Route::post('save', 'GoalController@save')->name("store-website.goal.save");
            Route::prefix('{goalId}')->group(function () {
                Route::get('edit', 'GoalController@edit')->name("store-website.goal.edit");
                Route::get('delete', 'GoalController@delete')->name("store-website.goal.delete");
                Route::get('remarks', 'GoalController@remarks')->name("store-website.goal.remarks");
                Route::post('remarks', 'GoalController@storeRemarks')->name("store-website.goal.remarks.store");
            });
        });
    });    

    Route::prefix('brand')->group(function () {
        Route::get('/', 'BrandController@list')->name("store-website.brand.list");
        Route::get('records', 'BrandController@records')->name("store-website.brand.records");
        Route::post('push-to-store', 'BrandController@pushToStore')->name("store-website.brand.push-to-store");
    });

    Route::prefix('price-override')->group(function () {
        Route::get('/', 'PriceOverrideController@index')->name("store-website.price-override.index");
        Route::get('records', 'PriceOverrideController@records')->name("store-website.price-override.records");
        Route::post('save', 'PriceOverrideController@save')->name("store-website.price-override.save");
        Route::get('calculate', 'PriceOverrideController@calculate')->name("store-website.price-override.calculate");
        Route::prefix('{id}')->group(function () {
            Route::get('edit', 'PriceOverrideController@edit')->name("store-website.price-override.edit");
            Route::get('delete', 'PriceOverrideController@delete')->name("store-website.price-override.delete");
        });
    });

});

Route::prefix('site-development')->group(function () {
    Route::get('/{id?}', 'SiteDevelopmentController@index')->name("site-development.index");
    Route::post('/save-category', 'SiteDevelopmentController@addCategory')->name("site-development.category.save");

    Route::post('/edit-category', 'SiteDevelopmentController@editCategory')->name("site-development.category.edit");

    Route::post('/save-development', 'SiteDevelopmentController@addSiteDevelopment')->name("site-development.save");
});

Route::prefix('site-development-status')->group(function () {
    Route::get('/', 'SiteDevelopmentStatusController@index')->name('site-development-status.index');
    Route::get('records', 'SiteDevelopmentStatusController@records')->name('site-development-status.records');
    Route::post('save', 'SiteDevelopmentStatusController@save')->name('site-development-status.save');
    Route::post('merge-status', 'SiteDevelopmentStatusController@mergeStatus')->name('site-development-status.merge-status');
    Route::prefix('{id}')->group(function () {
        Route::get('edit', 'SiteDevelopmentStatusController@edit')->name('site-development-status.edit');
        Route::get('delete', 'SiteDevelopmentStatusController@delete')->name('site-development-status.delete');
    });
});
