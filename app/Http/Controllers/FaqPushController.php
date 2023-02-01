<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProceesPushFaq;
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
	    	ProceesPushFaq::dispatch($data['id']);

			return response()->json(['code' => 200, 'data' => [], 'message' => 'Record Added']);

    	} catch (Exception $e) {
	    		return response()->json(['code' => 400, 'data' => [], 'message' => $e->getMessage()]);    		   		
    	}
    }   
}
