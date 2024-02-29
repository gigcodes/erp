<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver'                  => 'sqlite',
            'url'                     => env('DATABASE_URL'),
            'database'                => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix'                  => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'read' => [
                'host' => [
                    env('DB_HOST_READ', '127.0.0.1'),
                ],
            ],
            'write' => [
                'host' => [
                    env('DB_HOST', '127.0.0.1'),
                ],
            ],
            'sticky'         => true,
            'driver'         => 'mysql',
            'url'            => env('DATABASE_URL'),
            'host'           => env('DB_HOST', '127.0.0.1'),
            'port'           => env('DB_PORT', '3306'),
            'database'       => env('DB_DATABASE', 'erp'),
            'username'       => env('DB_USERNAME', 'root'),
            'password'       => env('DB_PASSWORD', ''),
            'unix_socket'    => env('DB_SOCKET', ''),
            'charset'        => 'utf8mb4',
            'collation'      => 'utf8mb4_unicode_ci',
            'prefix'         => '',
            'prefix_indexes' => true,
            'strict'         => false,
            'engine'         => 'InnoDB',
        ],
        'mysql_read' => [
            'sticky'         => true,
            'driver'         => 'mysql',
            'url'            => env('DATABASE_URL'),
            'host'           => env('DB_HOST_READ', '127.0.0.1'),
            'port'           => env('DB_PORT', '3306'),
            'database'       => env('DB_DATABASE', 'erp'),
            'username'       => env('DB_USERNAME', 'root'),
            'password'       => env('DB_PASSWORD', ''),
            'unix_socket'    => env('DB_SOCKET', ''),
            'charset'        => 'utf8mb4',
            'collation'      => 'utf8mb4_unicode_ci',
            'prefix'         => '',
            'prefix_indexes' => true,
            'strict'         => false,
            'engine'         => 'InnoDB',
        ],
        'brands-labels' => [
            'driver'   => 'mysql',
            'host'     => env('BRANDS_HOST', 'erp'),
            'database' => env('BRANDS_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],
        'avoirchic' => [
            'driver'   => 'mysql',
            'host'     => env('AVOIRCHIC_HOST', 'erp'),
            'database' => env('AVOIRCHIC_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],
        'olabels' => [
            'driver'   => 'mysql',
            'host'     => env('OLABELS_HOST', 'erp'),
            'database' => env('OLABELS_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],
        'sololuxury' => [
            'driver'   => 'mysql',
            'host'     => env('SOLOLUXURY_HOST', 'erp'),
            'database' => env('SOLOLUXURY_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],

        'suvandnet' => [
            'driver'   => 'mysql',
            'host'     => env('SUVANDNAT_HOST', 'erp'),
            'database' => env('SUVANDNAT_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],
        'suvandnat' => [
            'driver'   => 'mysql',
            'host'     => env('SUVANDNAT_HOST', 'erp'),
            'database' => env('SUVANDNAT_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],
        'thefitedit' => [
            'driver'   => 'mysql',
            'host'     => env('THEFITEDIT_HOST', 'erp'),
            'database' => env('THEFITEDIT_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],
        'theshadesshop' => [
            'driver'   => 'mysql',
            'host'     => env('THESHADSSHOP_HOST', 'erp'),
            'database' => env('THESHADSSHOP_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],
        'upeau' => [
            'driver'   => 'mysql',
            'host'     => env('UPEAU_HOST', 'erp'),
            'database' => env('UPEAU_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],
        'veralusso' => [
            'driver'   => 'mysql',
            'host'     => env('VERALUSSO_HOST', 'erp'),
            'database' => env('VERALUSSO_DB', 'erp'),
            'username' => env('MAGENTO_DB_USER', 'root'),
            'password' => env('MAGENTO_DB_PASSWORD', ''),
            'strict'   => false,
        ],
        'tracker' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => env('DB_DATABASE', 'erp'),
            'username'  => env('DB_USERNAME', 'root'),
            'password'  => env('DB_PASSWORD', ''),
            'strict'    => false,    // to avoid problems on some MySQL installs
            'engine'    => 'MyISAM',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
        'pgsql' => [
            'driver'         => 'pgsql',
            'url'            => env('DATABASE_URL'),
            'host'           => env('DB_HOST', '127.0.0.1'),
            'port'           => env('DB_PORT', '5432'),
            'database'       => env('DB_DATABASE', 'forge'),
            'username'       => env('DB_USERNAME', 'forge'),
            'password'       => env('DB_PASSWORD', ''),
            'charset'        => 'utf8',
            'prefix'         => '',
            'prefix_indexes' => true,
            'schema'         => 'public',
            'sslmode'        => 'prefer',
        ],

        'sqlsrv' => [
            'driver'         => 'sqlsrv',
            'url'            => env('DATABASE_URL'),
            'host'           => env('DB_HOST', 'localhost'),
            'port'           => env('DB_PORT', '1433'),
            'database'       => env('DB_DATABASE', 'forge'),
            'username'       => env('DB_USERNAME', 'forge'),
            'password'       => env('DB_PASSWORD', ''),
            'charset'        => 'utf8',
            'prefix'         => '',
            'prefix_indexes' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'predis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'predis'),
            'prefix'  => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
        ],

        'default' => [
            'url'      => env('REDIS_URL'),
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port'     => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
        ],

        'cache' => [
            'url'      => env('REDIS_URL'),
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port'     => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CACHE_DB', 1),
        ],

    ],

];
