<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ImQueue;
use \Carbon\Carbon;
use App\Helpers\InstantMessagingHelper;

class InstantMessagingController extends Controller
{
    public function getMessage($client,$numberFrom)
    {
    	$queues = ImQueue::select('text','image','number_to')->where('im_client',$client)->where('number_from',$numberFrom)->orderBy('created_at','asc')->orderBy('priority','desc')->take(1)->get();
    	//dd(count($queues));
    	if($queues == null || $queues == '' || count($queues) == 0){
    		$message = array('errors' => 'The queue is empty');
    		return json_encode($message,400);
    	}
    	$output = array();
    	foreach ($queues as $queue) {
    		if($queue->send_after != null && $queue->send_after >= Carbon::now()){
    			continue;
    		}
    		if($queue->text != null){
    			$text = array('phone' => $queue->number_to , 'body' => $queue->text);
    			array_push($output, $text);
    		}elseif($queue->image != null){
    			$image = json_decode($queue->image);
    			$image = array('phone' => $queue->number_to, 'body' => $image->body , 'filename' => $image->filename ,'caption' => $image->caption);
    			array_push($output, $image);
    		}
    	}
		return json_encode($output,200);
    }
}
