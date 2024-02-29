<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogRequest extends Model
{
    public static function log($startTime, $magentoURL, $method, $request, $response, $httpcode, $api_name, $api_method)
    {
        $endTime        = date('Y-m-d H:i:s');
        $timeTaken      = strtotime($endTime) - strtotime($startTime);
        $r              = new LogRequest;
        $r->request     = $request;
        $r->response    = ! empty($response) ? json_encode($response) : json_encode([]);
        $r->url         = $magentoURL;
        $r->ip          = request()->ip() ?: null;
        $r->method      = $method;
        $r->status_code = $httpcode;
        $r->time_taken  = $timeTaken;
        $r->start_time  = $startTime;
        $r->end_time    = $endTime;
        $r->api_name    = $api_name;
        $r->message     = '';
        $r->method_name = $api_method;
        $r->is_send     = 1;
        $r->save();
    }
}
