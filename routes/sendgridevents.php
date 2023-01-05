<?php

use App\Http\Controllers\App;
use App\Http\Middleware\SendgridEventMiddleware;
use Illuminate\Support\Facades\Route;

<<<<<<< HEAD
Route::middleware(SendgridEventMiddleware::class)->group(function () {
    Route::any(config('sendgridevents.webhook_url'), [App\Http\Controllers\WebhookController::class, 'post'])->name('sendgrid.webhook');
=======
Route::group([
    'namespace' => 'App\Http\Controllers',
    'middleware' => SendgridEventMiddleware::class,
], function () {
    //Route::any(config('sendgridevents.webhook_url'), 'WebhookController@post')->name('sendgrid.webhook');
>>>>>>> master

    // Route::post(
    //     config('sendgridevents.webhook_url'),
    //     [
    //         'as' => 'sendgrid.webhook',
    //         'uses' => 'WebhookController@post'
    //     ]
    // );
});
