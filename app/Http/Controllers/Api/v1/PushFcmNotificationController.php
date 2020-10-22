<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\StoreWebsite;
use App\PushFcmNotification;
class PushFcmNotificationController extends Controller
{
    public function create(request $request){
        $validator = Validator::make($request->all(),[
            'token'=>'required',
            'website'=>'exists:store_websites,website'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => 'Please check validation errors !', 'errors' => $validator->errors()], 400);
        }
        $storeweb = StoreWebsite::where('website', $request->website)->first();
        $push_data = [
            'url'=>$request->website,
            'token'=>$request->token,
            'title'=>$storeweb->title,
            'body'=>$storeweb->description,
            'store_website_id'=>$storeweb->id,
            'created_by'=>'user'
        ];
        $push = PushFcmNotification::create($push_data);
        if(!$push){
            return response()->json(['status' => 'failed', 'message' => 'Unable to create notification !'], 500);    
        }
        return response()->json(['status' => 'success', 'message' => 'Notification created successfully !'], 200);
    }
}
