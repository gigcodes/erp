<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\StoreWebsite;
use App\FcmToken;
class PushFcmNotificationController extends Controller
{
    public function create(request $request){
        $validator = Validator::make($request->all(),[
            'token'=>'required',
            'website'=>'exists:store_websites,website'
        ]);
        if ($validator->fails()) {
            $message = $this->generate_erp_response("notification.failed.validation",0, $default = 'Please check validation errors !', request('lang_code'));
            return response()->json(['status' => 'failed', 'message' => $message, 'errors' => $validator->errors()], 400);
        }
        $storeweb = StoreWebsite::where('website', $request->website)->first();
        $token_data = [
            
            'token'=>$request->token,
            'store_website_id'=>$storeweb->id,
        ];
        $insert = FcmToken::create($token_data);
        if(!$insert){
            $message = $this->generate_erp_response("notification.failed",$storeweb->id, $default = 'Unable to create notification !', request('lang_code'));
            return response()->json(['status' => 'failed', 'message' => $message], 500);    
        }
        $message = $this->generate_erp_response("notification.success",$storeweb->id, $default = 'Notification created successfully !', request('lang_code'));
        return response()->json(['status' => 'success', 'message' => $message], 200);
    }
}
