<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 30,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],

        'listMagento' => [
            'driver' => 'daily',
            'path' => storage_path('logs/list-magento.log'),
            'days' => 7,
        ],

        'productUpdates' => [
            'driver' => 'daily',
            'path' => storage_path('logs/product-updates.log'),
            'days' => 7,
        ],

        'chatapi' => [
            'driver' => 'daily',
            'path' => storage_path('logs/chatapi/chatapi.log'),
            'level' => 'debug',
            'days' => 7,
        ],

        'customerDnd' => [
            'driver' => 'daily',
            'path' => storage_path('logs/customers/dnd.log'),
            'level' => 'debug',
        ],

        'customer' => [
            'driver' => 'daily',
            'path' => storage_path('logs/general/general.log'),
            'level' => 'debug',
            'days' => 7,
        ],

        'whatsapp' => [
            'driver' => 'daily',
            'path' => storage_path('logs/whatsapp/whatsapp.log'),
            'days' => 7,
        ],

        'scraper' => [
            'driver' => 'daily',
            'path' => storage_path('logs/scraper/scraper.log'),
            'days' => 7,
        ],

        'update_category_job' => [
            'driver' => 'daily',
            'path' => storage_path('logs/category_job/category_job.log'),
            'days' => 7,
        ],

        'update_color_job' => [
            'driver' => 'daily',
            'path' => storage_path('logs/color_job/color_job.log'),
            'days' => 7,
        ],

        'broadcast_log' => [
            'driver' => 'daily',
            'path' => storage_path('logs/general/broadcast.log'),
            'days' => 1,
        ],

        'hubstaff_activity_command' => [
            'driver' => 'daily',
            'path' => storage_path('logs/hubstaff-activity-command/hubstaff-activity-command.log'),
            'days' => 7,
        ],

        'insta_message_queue_by_rate_limit' => [
            'driver' => 'daily',
            'path' => storage_path('logs/insta-message-queue-by-rate-limit/insta-message-queue-by-rate-limit.log'),
            'days' => 7,
        ],

        'product_push_information_csv' => [
            'driver' => 'daily',
            'path' => storage_path('logs/product-push-information-csv/product-push-information-csv.log'),
            'days' => 7,
        ],

        'product-thumbnail' => [
            'driver' => 'daily',
            'path' => storage_path('logs/product-thumbnail/product-thumbnail-command.log'),
            'days' => 7,
        ],

        'scrapper_images' => [
            'driver' => 'daily',
            'path' => storage_path('logs/scrapper_images/scrapper_images.log'),
            'days' => 7,
        ],

        'social_webhook' => [
            'driver' => 'daily',
            'path' => storage_path('logs/social_webhook/social_webhook.log'),
            'days' => 7,
        ],

        'time_doctor_activity_command' => [
            'driver' => 'daily',
            'path' => storage_path('logs/time-doctor-activity-command/time-doctor-activity-command.log'),
            'days' => 7,
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'github_error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/github_error.log'),
            'level' => 'error',
            'days' => 7,
        ],

        'magento_problem_error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/magento_problem_error.log'),
            'level' => 'error',
            'days' => 7,
        ],
    ],

];
