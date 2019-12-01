<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Input;
use App\Customer;
use App\ChatMessage;
use App\CustomerLiveChat;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Plank\Mediable\Mediable;


class LiveChatController extends Controller
{
	//Webhook
	public function incoming(Request $request)
	{
		$receivedJson = json_decode($request->getContent());
		
		if(isset($receivedJson->event_type)){
			//When customer Starts chat
			if($receivedJson->event_type == 'chat_started'){
				
				///Getting the chat
				$chat = $receivedJson->chat;
				
				//Getting Agent 
				$agent = $chat->agents;
				// name": "SoloLuxury"
				// +"login": "yogeshmordani@icloud.com"
				$chat_survey = $receivedJson->pre_chat_survey;
				$detials = array();
				foreach($chat_survey as $survey){
					$label = strtolower($survey->label);
					
					if (strpos($label, 'name') !== false) {
						array_push($detials,$survey->answer);
					}
					if (strpos($label, 'e-mail') !== false) {
						array_push($detials,$survey->answer);
					}
					if (strpos($label, 'phone') !== false) {
						array_push($detials,$survey->answer);
					}
				}
				
				$name = $detials[0];
				$email = $detials[1];
				$phone = $detials[2];
				//Check if customer exist 

				$customer = Customer::where('email',$email)->first();	

				//Save Customer
				if($customer == null && $customer == ''){
					$customer = new Customer;
					$customer->name = $name;
					$customer->email = $email;
					$customer->phone = $phone;
					$customer->save();
				}
				
			}
		}

		if(isset($receivedJson->action)){
			//Incomg Event
			if($receivedJson->action == 'incoming_event'){
				
				//Chat Details 
				$chatDetails = $receivedJson->payload;
				//Chat Details
				$chatId = $chatDetails->chat_id;
				
				//Check if customer which has this id
				$customerLiveChat = CustomerLiveChat::where('thread',$chatId)->first();
				
				if($chatDetails->event->type == 'message'){

					$message = $chatDetails->event->text;
					
					$params = [
                    	'unique_id' => $chatDetails->chat_id,
                    	'message' => $message,
                    	'customer_id' => $customerLiveChat->customer_id,
                    	'approved' => 1,
                    	'status' => 2,
                    	'is_delivered' => 1,
					];
					
					// Create chat message
                	$chatMessage = ChatMessage::create($params);

				}

				if($chatDetails->event->type == 'file'){
					
					//creating message
					$params = [
                    	'unique_id' => $chatDetails->chat_id,
                    	'customer_id' => 41,
                    	'approved' => 1,
                    	'status' => 2,
                    	'is_delivered' => 1,
					];
					
					// Create chat message
					$chatMessage = ChatMessage::create($params);
					
					$url = $chatDetails->event->url;
					$jpg = \Image::make($url)->encode('jpg');
					$filename = $chatDetails->event->name;
                    $media = MediaUploader::fromString($jpg)->toDirectory('/gallery/' . floor($chatMessage->id / 10000) . '/' . $chatMessage->id)->useFilename($filename)->upload();
                    $chatMessage->attachMedia($media, config('constants.media_tags'));
				}
				
				// Add to chat_messages if we have a customer
			}

			if($receivedJson->action == 'incoming_chat_thread'){
				$chat = $receivedJson->payload->chat;
				$chatId = $chat->id;

				//Getting user
				$userEmail = $chat->users[0]->email;
				$userName = $chat->users[0]->name;
				
				$customer = Customer::where('email',$userEmail)->first();
				if($customer != '' && $customer != null){

					$customerChatId = new CustomerLiveChat;
					$customerChatId->customer_id = $customer->id;
					$customerChatId->thread = $chatId;
					$customerChatId->save();

				}else{
					$customer = new Customer;
					$customer->name = $userName;
					$customer->email = $userEmail;
					$customer->save();

					//Save Customer with Chat ID
					$customerChatId = new CustomerLiveChat;
					$customerChatId->customer_id = $customer->id;
					$customerChatId->thread = $chatId;
					$customerChatId->save();

				}
			}
		}
		
	}

	public function sendMessage(Request $request){
		
		//send text

		//Send File
	}
}

