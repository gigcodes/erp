<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchQueueController extends Controller
{
    public function index($type){
        $validator=Validator::make(['search_type'=>$type], [
            'search_type' =>'required|exists:search_queues,search_type'
          ]);
    
          if($validator->fails()) {
            $response['success']=false;
            $response['message']=$validator->errors()->first();
            
            return response()->json($response,400);
          }

          try{
            $list=\App\SearchQueue::paginate();
            $response['success']=true;
            $response['message']="success";
            $response['data']=$list;
            
            return response()->json($response,200);
          }catch(\Exception $e){
            $response['success']=false;
            $response['message']=$e->getMessage();
            
            return response()->json($response,500);
          }

          echo "<pre>";print_r($list);exit;

    }
}
