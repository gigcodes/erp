<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, SparkPost and others. This file provides a sane default
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'twitter' => [
        'client_id' => 'TVeU3YDvhBFL1ChkRJe0GF8AV',
        'client_secret' => 'LHNwbMFPZ71DvcBo5IRSYXnBYwFtNBWUbrTS6HBbtZOrucdJln',
        'redirect' => 'http://localhost/sololux-erp/auth/twitter/callback',
    ],

    'youtube' => [
        'client_id' => env('YOUTUBE_CLIENT_ID'),
        'client_secret' => env('YOUTUBE_CLIENT_SECRET'),
        'redirect' => env('YOUTUBE_REDIRECT_URI'),
    ],

    'twilio' => [
        /**
         * Taken from the environment
         */
        'accountSid' => env('TWILIO_ACCOUNT_SID'),
        'authToken' => env('TWILIO_AUTH_TOKEN'),
        'number' => env('TWILIO_NUMBER'),
        'missedCallsEmail' => env('MISSED_CALLS_EMAIL_ADDRESS'),

        /**
         * These are created with the workspace:create command for Artisan
         */
        'workspaceSid' => env('WORKSPACE_SID'),
        'workflowSid' => env('WORKFLOW_SID'),
        'postWorkActivitySid' => env('POST_WORK_ACTIVITY_SID'),
        'phoneToWorker' => env('PHONE_TO_WORKER'),

        /**
         * TaskRouter
         */
        'missedCallEvents' => ['workflow.timeout', 'task.canceled', 'task.reserved'],
        'leaveMessage' => 'Sorry, All agents are busy. Please leave a message. We will call you as soon as possible',
        'offlineMessage' => 'Your status has changed to Offline. Reply with "On" to get back Online',
    ],

    'zoom' => [
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
        'account_id' => env('ZOOM_ACCOUNT_ID'),
        'secret_token' => env('ZOOM_SECRET_TOKEN'),
    ],

];
