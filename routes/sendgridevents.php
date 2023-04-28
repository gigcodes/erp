<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SendgridEventMiddleware;

Route::middleware(SendgridEventMiddleware::class)->group(function () {
    Route::any(config('sendgridevents.webhook_url'), [\App\Http\Controllers\WebhookController::class, 'post'])->name('sendgrid.webhook');

    // Route::post(
    //     config('sendgridevents.webhook_url'),
    //     [
    //         'as' => 'sendgrid.webhook',
    //         'uses' => 'WebhookController@post'
    //     ]
    // );
});
