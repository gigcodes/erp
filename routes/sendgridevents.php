<?php

use App\Http\Controllers\App;
use App\Http\Middleware\SendgridEventMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(SendgridEventMiddleware::class)->group(function () {
    Route::any(config('sendgridevents.webhook_url'), [App\Http\Controllers\WebhookController::class, 'post'])->name('sendgrid.webhook');

    // Route::post(
    //     config('sendgridevents.webhook_url'),
    //     [
    //         'as' => 'sendgrid.webhook',
    //         'uses' => 'WebhookController@post'
    //     ]
    // );
});
