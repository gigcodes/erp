<?php

use Illuminate\Support\Facades\Route;
use Modules\LeadQueue\Http\Controllers\LeadQueueController;

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

Route::group([
    'prefix' => 'leadqueue',
    'middleware' => 'auth',
], function () {
    Route::get('/', [LeadQueueController::class, 'index']);
});

Route::group([
    'prefix' => 'lead-queue',
    'middleware' => 'auth',
], function () {
    Route::get('/', [LeadQueueController::class, 'index'])->name('lead-queue.index');
    Route::get('/approve', [LeadQueueController::class, 'approve'])->name('lead-queue.approve');
    Route::get('/approve/approved', [LeadQueueController::class, 'approved'])->name('lead-queue.approved');
    Route::get('/status', [LeadQueueController::class, 'status'])->name('lead-queue.status');
    Route::get('delete', [LeadQueueController::class, 'deleteRecord'])->name('lead-queue.delete.record');
    Route::group(['prefix' => 'records'], function () {
        Route::get('/', [LeadQueueController::class, 'records']);
        Route::post('/action-handler', [LeadQueueController::class, 'actionHandler']);
        // Route::prefix('{id}')->group(function() {
        // 	Route::get('delete', 'LeadQueueController@deleteRecord');
        // });
    });

    Route::group(['prefix' => 'report'], function () {
        Route::get('/', [LeadQueueController::class, 'report'])->name('lead-queue.report');
    });

    Route::group(['prefix' => 'setting'], function () {
        Route::post('update-limit', [LeadQueueController::class, 'updateLimit']);
        Route::get('recall', [LeadQueueController::class, 'recall']);
    });
});
