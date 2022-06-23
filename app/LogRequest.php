<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogRequest extends Model
{
    public static function log($startTime,$magentoURL,$method,$request,$response,$httpcode,$api_name,$api_method){
    	$endTime    = date("Y-m-d H:i:s");
        $timeTaken  = strtotime($endTime) - strtotime($startTime);
        $r              = new LogRequest;
        $r->url         = $magentoURL;
        $r->status_code = $httpcode;
        $r->method      = $method;
        $r->method_name = $api_method;
        $r->api_name    = $api_name;
        $r->request     = $request;
        $r->response    = !empty($response) ? json_encode($response) : json_encode([]);
        $r->message     = '';
        $r->start_time  = $startTime;
        $r->end_time    = $endTime;
        $r->time_taken  = $timeTaken;
        $r->is_send     = 1;
        $r->save();
    }
}
