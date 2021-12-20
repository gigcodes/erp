<?php

namespace App\Loggers;

use Illuminate\Database\Eloquent\Model;

class MailinglistIinfluencersLogs extends Model
{
   

    public static function log($result)
    {
        // Log result to database
        $log = new MailinglistIinfluencersLogs();
        $log->service = $result["service"];
        $log->maillist_id = $result["maillist_id"];
        $log->email = $result["email"];
        $log->name = $result["name"];
        $log->url = $result["url"];
        $log->request_data = $result["request_data"];
        $log->response_data = $result["response_data"];
        $log->save();

        // Return
        return $log;
    }
   

}
