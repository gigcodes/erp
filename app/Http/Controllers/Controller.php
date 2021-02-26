<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\ApiResponseMessage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function generate_erp_response($key, $store_website_id, $default = ""){
        $return_message = ApiResponseMessage::where('store_website_id',$store_website_id)->where('key',$key)->first();
        $message = $default;
        if(!empty($return_message)){
            $message = $return_message->value;
        }

        return $message;
    }
}
