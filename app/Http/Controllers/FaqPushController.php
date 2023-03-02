<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProceesPushFaq;
use App\Jobs\ProcessTranslateReply;
use App\Reply;

class FaqPushController extends Controller
{	
	//This API will put the records in Queue 
    function 	pushFaq(Request 	$request){

    	$data 	=	$request->all();

    	if(empty($data['id'])) {
			return response()->json(['code' => 400, 'data' => [], 'message' => 'One of the api parameter is missing']);    		
    	}

    	try {

            //Add the data for queue
            $insertArray        =   [];
            $insertArray[]      =   $data['id'];
    
            $replyInfo          =   Reply::find($data['id']);

            if(!empty($replyInfo->is_translate)){   //if FAQ translate is  available then send for FAQ
	    	  ProceesPushFaq::dispatch($insertArray);
            }
            else{   //If FAQ transation is not available then first set for translation
                ProcessTranslateReply::dispatch($replyInfo, \Auth::id());   //set for translation

                ProceesPushFaq::dispatch($insertArray);
            }

			return response()->json(['code' => 200, 'data' => [], 'message' => 'FAQ added in queue']);

    	} catch (Exception $e) {
	    		return response()->json(['code' => 400, 'data' => [], 'message' => $e->getMessage()]);    		   		
    	}
    }   


    function    pushFaqAll(Request  $request,   Reply   $Reply){

        
        $replyInfo      =   $Reply->select('replies.id','magento_url','api_token','replies.is_translate')
                                ->join('store_websites','store_websites.id','=','replies.store_website_id')
                                ->join('reply_categories as rep_cat','rep_cat.id','=','replies.category_id')
                                ->whereNotNull('store_websites.magento_url')
                                ->whereNotNull('store_websites.api_token')
                                ->get();


        if(empty($replyInfo)) {
            return response()->json(['code' => 400, 'data' => [], 'message' => 'No Record Found']);         
        }

        try {

            //Add the data for queue
            foreach ($replyInfo as $key => $value) {

                if(!empty($value->is_translate)){   //if FAQ translate is  available then send for FAQ
                    
                    $insertArray        =   [];
                    $insertArray[]      =   $value->id;

                    ProceesPushFaq::dispatch($insertArray);
                }
                else{   //If FAQ transation is not available then first set for translation

                    $insertArray        =   [];
                    $insertArray[]      =   $value->id;

                    $replyInformation   =   $Reply->find($value->id);

                    ProcessTranslateReply::dispatch($replyInformation, \Auth::id());   //set for translation

                    ProceesPushFaq::dispatch($insertArray);
                }

                //Pluck only ID from array 
                // $insertArray    =   $value->pluck('id');
                // $reqType = "pushFaqAll";
                // ProceesPushFaq::dispatch($insertArray->toArray(),$reqType);     //insert a Array and create a job of 100 at a time.
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Record Added']);

        } catch (Exception $e) {
                return response()->json(['code' => 400, 'data' => [], 'message' => $e->getMessage()]);                  
        }  

    }
}
