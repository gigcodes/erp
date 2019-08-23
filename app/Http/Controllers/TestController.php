<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Jobs\SendMessageToAll;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Controllers\WhatsAppController;
use App\Customer;


class TestController extends Controller
{
    public function test()
    {
    	$images = array('https://images.pexels.com/photos/248797/pexels-photo-248797.jpeg',
    		'https://images.pexels.com/photos/1006360/pexels-photo-1006360.jpeg',
    		'https://images.pexels.com/photos/1120367/pexels-photo-1120367.jpeg',
    		'https://images.pexels.com/photos/1656687/pexels-photo-1656687.jpeg',
    		'https://images.pexels.com/photos/846362/pexels-photo-846362.jpeg',
    		'https://images.pexels.com/photos/459225/pexels-photo-459225.jpeg',
    		'https://images.pexels.com/photos/462118/pexels-photo-462118.jpeg',
    		
    );
    	foreach ($images as $image) {
    	
    			$customer_phone = '918082488108';
    	$send_number = '919152731486';
    	$message = 'Check';
    	$file = 'https://upload.wikimedia.org/wikipedia/ru/3/33/NatureCover2001.jpg';
    	$chat_message_id = '6030';
    	app( WhatsAppController::class )->sendWithThirdApi($customer_phone, $send_number, $message, $image , $chat_message_id ,'');
    	}
    
    	 
    }

    public function login()
    {
    	 auth()->loginUsingId(56);
        return redirect()->back();
    }
}
