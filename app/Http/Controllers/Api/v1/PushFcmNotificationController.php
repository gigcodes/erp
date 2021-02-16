<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\StoreWebsite;
use App\FcmToken;
class PushFcmNotificationController extends Controller
{
    /**
     * @SWG\Post(
     *   path="/notification/create",
     *   tags={"Notification"},
     *   summary="Create notification",
     *   operationId="create-notification",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true, 
     *          type="string" 
     *      ),
     * )
     *
     */
    public function create(request $request){
        $validator = Validator::make($request->all(),[
            'token'=>'required',
            'website'=>'exists:store_websites,website'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => 'Please check validation errors !', 'errors' => $validator->errors()], 400);
        }
        $storeweb = StoreWebsite::where('website', $request->website)->first();
        $token_data = [
            
            'token'=>$request->token,
            'store_website_id'=>$storeweb->id,
        ];
        $insert = FcmToken::create($token_data);
        if(!$insert){
            return response()->json(['status' => 'failed', 'message' => 'Unable to create notification !'], 500);    
        }
        return response()->json(['status' => 'success', 'message' => 'Notification created successfully !'], 200);
    }
}
