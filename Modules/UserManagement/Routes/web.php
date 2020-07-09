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

Route::prefix('user-management')->group(function() {
    Route::get('/', 'UserManagementController@index')->name("user-management.index");
    Route::get('/edit/{id}', 'UserManagementController@edit')->name("user-management.edit");
    Route::get('/role/{id}', 'UserManagementController@getRoles')->name("user-management.get-role");
    Route::post('/role/{id}', 'UserManagementController@submitRoles')->name("user-management.submit-role");
    Route::get('/permission/{id}', 'UserManagementController@getPermission')->name("user-management.get-permission");
    Route::post('/permission/{id}', 'UserManagementController@submitPermission')->name("user-management.submit-permission");
    Route::post('/add-permission', 'UserManagementController@addNewPermission')->name("user-management.add-permission");
    Route::get('/show/{id}', 'UserManagementController@show')->name("user-management.show");
    Route::patch('/update/{id}', 'UserManagementController@update')->name("user-management.update");
    Route::post('/{id}/activate', 'UserManagementController@activate')->name("user-management.activate");
    Route::get('track/{id}', 'UserManagementController@usertrack')->name("user-management.usertrack");
    Route::get('/paymentInfo/{id}', 'UserManagementController@paymentInfo')->name("user-management.payment-info");
    Route::get('payments/{id}', 'UserManagementController@userPayments')->name("user-management.payments");
    Route::post('payments/{id}', 'UserManagementController@savePayments')->name("user-management.savePayments");
    Route::post('/add-new-method', 'UserManagementController@addPaymentMethod')->name("user-management.add-payment-method");
    Route::get('/records', 'UserManagementController@records')->name("user-management.records");
});
