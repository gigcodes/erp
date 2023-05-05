<?php

namespace App\Helpers;

use Carbon\Carbon;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LogHelper
{
    public static function createCustomLogForCron($fileName = 'laravel', $message = array())
    {
        $currentDate = Carbon::now()->format('Y-m-d');

        $fileName = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '-', $fileName);

        //first parameter passed to Monolog\Logger sets the logging channel name
        $cronLog = new Logger($fileName);
        $cronLog->pushHandler(new StreamHandler(storage_path('logs/'.$fileName.'-'.$currentDate.'.log')), Logger::INFO);
        $cronLog->info('Error', $message);
    }
}
