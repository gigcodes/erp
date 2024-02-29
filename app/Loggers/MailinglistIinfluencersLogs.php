<?php

namespace App\Loggers;

use Illuminate\Database\Eloquent\Model;

class MailinglistIinfluencersLogs extends Model
{
    protected $table = 'mailinglist_iinfluencers_logs';

    public static function log($text)
    {
        // Log result to database
        $log          = new MailinglistIinfluencersLogs();
        $log->message = $text;
        $log->save();

        // Return
        return $log->id;
    }
}
