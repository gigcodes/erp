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

Route::prefix('message-queue')->group(function() {
    Route::get('/', 'MessageQueueController@index');
    Route::prefix('records')->group(function() {
		Route::get('/', 'MessageQueueController@records');
		Route::post('action-handler','MessageQueueController@actionHandler');
		Route::prefix('{id}')->group(function() {
			Route::get('delete', 'MessageQueueController@deleteRecord');
		});
	});    	
});
