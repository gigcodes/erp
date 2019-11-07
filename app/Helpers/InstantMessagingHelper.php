<?php

namespace App\Helpers;
use App\ImQueue;


class InstantMessagingHelper {


	public function sendInstantMessage($numberTo , $text = null , $image = null, $priority = null, $numberFrom = null, $client = null, $sendAfter = null)
	{
		if($image != null || $text != null){
			if($numberTo == '' || $numberTo == null){
				return redirect()->back()->withErrors('Please Provide To Send');
			}
			if($numberFrom == null){
				$numberFrom = env('DEFAULT_SEND_NUMBER');
			}
			if($client == null){
				$client = 'whatsapp';
			}

			$queue = new ImQueue();
			$queue->im_client = $client;
			$queue->number_to = $numberTo;
			$queue->number_from = $numberFrom;
		
			if($image != null && $text != null){
				$queue->image = self::encodeImage($text,$image);
			}elseif($image != null){
				$queue->image = self::encodeImage('',$image);
			}else{
				$queue->text = $text;
			}

			if($priority == null){
				$queue->priority = 0;	
			}else{
				$queue->priority = $priority;
			}

			$queue->send_after = $sendAfter;
			$queue->save();
			return redirect()->back()->withSuccess('Mesage Saved');
		}else{
			return redirect()->back()->withErrors('Please Provide with image link or message');
		}
		
	}

	public function encodeImage($text = null , $image)
	{
		$filename = basename($image);
		if($text == null){
			$image = array('body' => $image , 'filename' => $filename , 'caption' => ''); 
		}else{
			$image = array('body' => $image , 'filename' => $filename , 'caption' => $text); 	
		}
		return json_encode($image);
      
	}

}