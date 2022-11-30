<?php

use App\Http\Middleware\SendgridEventMiddleware;

Route::group([
    'namespace' => 'App\Http\Controllers',
    'middleware' => SendgridEventMiddleware::class,
], function () {
    Route::any(config('sendgridevents.webhook_url'), 'WebhookController@post')->name('sendgrid.webhook');

    // Route::post(
    //     config('sendgridevents.webhook_url'),
    //     [
    //         'as' => 'sendgrid.webhook',
    //         'uses' => 'WebhookController@post'
    //     ]
    // );
});
