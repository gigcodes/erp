<?php

/**
 * SnappyPDF configuration options.
 *
 * Changes to these config files are not supported by BookStack and may break upon updates.
 * Configuration should be altered via the `.env` file or environment variables.
 * Do not edit this file unless you're happy to maintain any changes yourself.
 */

return [
    'pdf' => [
        'enabled' => true,
        'binary' => file_exists(base_path('wkhtmltopdf')) ? base_path('wkhtmltopdf') : env('WKHTMLTOPDF', false),
        'timeout' => false,
        'options' => [],
        'env' => [],
    ],
    'image' => [
        'enabled' => false,
        'binary' => '/usr/local/bin/wkhtmltoimage',
        'timeout' => false,
        'options' => [],
        'env' => [],
    ],
];
