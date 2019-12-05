<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Input;
use App\Customer;
use App\ChatMessage;
use App\CustomerLiveChat;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Plank\Mediable\Mediable;
use App\User;
use App\LiveChatUser;
use App\LivechatincSetting;


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
				
				if($customer == '' && $customer == null && $phone != ''){
					$customer = Customer::where('phone',$phone)->first();
				}	

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
				
				//update to not seen
				if($customerLiveChat != '' && $customerLiveChat != null){
					$customerLiveChat->seen = 0;
					$customerLiveChat->update();
				}
				if($chatDetails->event->type == 'message'){
					
					$message = $chatDetails->event->text;
					$author_id = $chatDetails->event->author_id;
					
					// Finding Agent 
					$agent = User::where('email',$author_id)->first();
					
					if($agent != '' && $agent != null){
						$userID = $agent->id;
					}else{
						$userID = null;
					}
					
					$params = [
                    	'unique_id' => $chatDetails->chat_id,
                    	'message' => $message,
                    	'customer_id' => $customerLiveChat->customer_id,
                    	'approved' => 1,
                    	'status' => 2,
						'is_delivered' => 1,
						'user_id' => $userID,
						'message_application_id' => 2,
					];
					
					// Create chat message
                	$chatMessage = ChatMessage::create($params);
					
				}

				if($chatDetails->event->type == 'file'){
					
					$author_id = $chatDetails->event->author_id;
					
					// Finding Agent 
					$agent = User::where('email',$author_id)->first();
					
					if($agent != '' && $agent != null){
						$userID = $agent->id;
					}else{
						$userID = null;
					}

					//creating message
					$params = [
                    	'unique_id' => $chatDetails->chat_id,
                    	'customer_id' => $customerLiveChat->customer_id,
                    	'approved' => 1,
                    	'status' => 2,
						'is_delivered' => 1,
						'user_id' => $userID,
						'message_application_id' => 2,
					];
					
					// Create chat message
					$chatMessage = ChatMessage::create($params);
					$numberPath = substr($from, 0, 3) . '/' . substr($from, 3);
					$url = $chatDetails->event->url;
					$jpg = \Image::make($url)->encode('jpg');
					$filename = $chatDetails->event->name;
                    $media = MediaUploader::fromString($jpg)->toDirectory('/chat-messages/' . $numberPath)->useFilename($filename)->upload();
                    $chatMessage->attachMedia($media, config('constants.media_tags'));
				}

				if($chatDetails->event->type == 'system_message'){
					
					$customerLiveChat = CustomerLiveChat::where('thread',$chatId)->first();
					if($customerLiveChat != '' && $customerLiveChat != null){
						$customerLiveChat->status = 0;
						$customerLiveChat->seen = 1;
						$customerLiveChat->update();
					}
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
					//Find if its has ID
					$chatID = CustomerLiveChat::where('customer_id',$customer->id)->first();
					if($chatID == null && $chatID == ''){
						$customerChatId = new CustomerLiveChat;
						$customerChatId->customer_id = $customer->id;
						$customerChatId->thread = $chatId;
						$customerChatId->status = 1;
						$customerChatId->seen = 0;
						$customerChatId->save();
					}else{
						$chatID->customer_id = $customer->id;
						$chatID->thread = $chatId;
						$chatID->status = 1;
						$chatID->seen = 0;
						$chatID->update();
					}
				}else{
					$customer = new Customer;
					$customer->name = $userName;
					$customer->email = $userEmail;
					$customer->phone = null;
					$customer->save();

					//Save Customer with Chat ID
					$customerChatId = new CustomerLiveChat;
					$customerChatId->customer_id = $customer->id;
					$customerChatId->thread = $chatId;
					$customerChatId->status = 1;
					$customerChatId->seen = 0;
					$customerChatId->save();

				}
			}

			if($receivedJson->action == 'thread_closed'){
				$chatId = $receivedJson->payload->chat_id;
				
				$customerLiveChat = CustomerLiveChat::where('thread',$chatId)->first();
				
					if($customerLiveChat != '' && $customerLiveChat != null){
						$customerLiveChat->thread = null;
						$customerLiveChat->status = 0;
						$customerLiveChat->seen = 1;
						$customerLiveChat->update();
						
					}
			}
		}
		
	}

	public function sendMessage(Request $request){
			
		    $login = \Config('livechat.account_id');
            $password = \Config('livechat.password');
			$chatId = $request->id;
			$message = $request->message;
			
			//Get Thread ID From Customer Live Chat
			$customer = CustomerLiveChat::where('customer_id',$chatId)->first();
			
			if($customer != '' && $customer != null){
				$thread = $customer->thread;
				
			}else{
				return response()->json([
            	'status' => 'errors'
        		]);
			}
			$post = array('chat_id' => $thread,'event' => array('type' => 'message','text' => $message,'recipients' => 'all',));
		    $post = json_encode($post);
			
			$curl = curl_init();

			curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.livechatinc.com/v3.1/agent/action/send_event",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "$post",
			CURLOPT_HTTPHEADER => array(
				"Authorization: Basic NTYwNzZkODktZjJiZi00NjUxLTgwMGQtNzE5YmEyNTYwOWM5OmRhbDpUQ3EwY2FZYVRrMndCTHJ3dTgtaG13",
				"Content-Type: application/json",
			),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				return response()->json([
            	'status' => 'errors'
        		]);
			} else {
				$response = json_decode($response);
				if(isset($response->error)){
					return response()->json([
            			'status' => 'errors'
        			]);
				}else{
					return response()->json([
            			'status' => 'success'
        			]);
				}
			}
	}

	public function setting(){
		$liveChatUsers = LiveChatUser::all();
		$setting = LivechatincSetting::first();
		$users = User::where('is_active',1)->get();
		return view('livechat.setting', compact('users','liveChatUsers','setting'));
	}

	public function remove(Request $request){

		$users = LiveChatUser::findorfail($request->id);
		$users->delete();
		
		return response()->json(['success' => 'success'], 200);
	}

	public function save(Request $request){
		
		if($request->username != '' || $request->key != ''){
			$checkIfExist = LivechatincSetting::all();
			if(count($checkIfExist) == 0){
				$setting = new LivechatincSetting;
				$setting->username = $request->username;
				$setting->key = $request->key;
				$setting->save();
			}else{
				$setting = LivechatincSetting::first();
				$setting->username = $request->username;
				$setting->key = $request->key;
				$setting->update();
			}
			
		}

		if($request->users != null && $request->users != ''){
			$users = $request->users;
			foreach($users as $user){
				
				$userCheck = LiveChatUser::where('user_id',$user)->first();
				if($userCheck != '' && $userCheck != null){
					continue;
				 }
				$userss = new LiveChatUser();
				$userss->user_id = $user;
				$userss->save();
				
			}
			
		}

		return redirect()->back()->withSuccess(['msg', 'Saved']);
	}
	//Send 
	// public function sendFile(Request $request){
			
	// 		$img = self::sendImage($request);
	// 		dd($img);
	// 		//get LIVE CHAT URL FROM PATH
			
	// 		$chatId = $request->id;
	// 		$message = $request->file;

	// 		$file = 'https://cdn.livechat-static.com/api/file/lc/tmp/attachments/11434003/c9f86c49804ea3ebee7cadbafa5d779a/Screen%20Shot%202019-12-01%20at%2012.46.12%20AM.png';
	// 		//Get Thread ID From Customer Live Chat
	// 		$customer = CustomerLiveChat::where('customer_id',$chatId)->first();
			
	// 		if($customer != '' && $customer != null){
	// 			$thread = $customer->thread;
				
	// 		}else{
	// 			return response()->json([
    //         	'status' => 'errors'
    //     		]);
	// 		}
	// 		$post = array('chat_id' => $thread,'event' => array('type' => 'file','content_type' => $file,'created_at' => '2017-10-12T15:19:21.010200Z', 'url' => $file , 'recipients' => 'all'));
	// 	    $post = json_encode($post);
			
	// 		$curl = curl_init();

	// 		curl_setopt_array($curl, array(
	// 		CURLOPT_URL => "https://api.livechatinc.com/v3.1/agent/action/send_event",
	// 		CURLOPT_RETURNTRANSFER => true,
	// 		CURLOPT_ENCODING => "",
	// 		CURLOPT_MAXREDIRS => 10,
	// 		CURLOPT_TIMEOUT => 30,
	// 		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	// 		CURLOPT_CUSTOMREQUEST => "POST",
	// 		CURLOPT_POSTFIELDS => "$post",
	// 		CURLOPT_HTTPHEADER => array(
	// 			"Authorization: Basic NTYwNzZkODktZjJiZi00NjUxLTgwMGQtNzE5YmEyNTYwOWM5OmRhbDpUQ3EwY2FZYVRrMndCTHJ3dTgtaG13",
	// 			"Content-Type: application/json",
	// 		),
	// 		));

	// 		$response = curl_exec($curl);
	// 		$err = curl_error($curl);

	// 		curl_close($curl);

	// 		if ($err) {
	// 			return response()->json([
    //         	'status' => 'errors'
    //     		]);
	// 		} else {
	// 			$response = json_decode($response);
	// 			if(isset($response->error)){
	// 				return response()->json([
    //         			'status' => 'errors'
    //     			]);
	// 			}else{
	// 				return response()->json([
    //         			'status' => 'success'
    //     			]);
	// 			}
	// 		}
	// }

	public function getChats(Request $request)
	{
		$chatId = $request->id;

		//put session 
		session()->put('chat_customer_id', $chatId);
		
		//update chat has been seen
		$customer = CustomerLiveChat::where('customer_id',$chatId)->first();

		if($customer != '' && $customer != null){
			$customer->seen = 1;
			$customer->update();
		}

		$messages = ChatMessage::where('customer_id',$chatId)->where('message_application_id',2)->get();
		//getting customer name from chat
		$customer = Customer::findorfail($chatId);
		$name = $customer->name;
		if(count($messages) != 0){
			foreach ($messages as $message) {
			if($message->user_id != 0){
				$messagess[] = '<div class="d-flex justify-content-end mb-4"><div class="msg_cotainer_send"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div><div class="msg_cotainer">'.$message->message.'<span class="msg_time">'.\Carbon\Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span></div></div>';
			}else{
				$messagess[] = '<div class="d-flex justify-content-start mb-4"><div class="img_cont_msg"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div><div class="msg_cotainer">'.$message->message.'<span class="msg_time">'.\Carbon\Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span></div></div>';
			}
			}

		}
		
		if(!isset($messagess)){
				$messagess[] = '<div class="d-flex justify-content-end mb-4"><div class="msg_cotainer_send"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div><div class="msg_cotainer">New Customer For Chat<span class="msg_time">'.\Carbon\Carbon::createFromTimeStamp(strtotime(now()))->diffForHumans().'</span></div></div>';
		}

		$count = CustomerLiveChat::where('seen',0)->count();
		
		return response()->json([
						'status' => 'success',
						'data' => array('id' => $chatId ,'count' => $count, 'message' => $messagess , 'name' => $name),
        			]);
	}
	
	public function getChatMessagesWithoutRefresh()
	{
		if(session()->has('chat_customer_id'))
		{
			$chatId = session()->get('chat_customer_id');
			$messages = ChatMessage::where('customer_id',$chatId)->where('message_application_id',2)->get();
			//getting customer name from chat
			$customer = Customer::findorfail($chatId);
			$name = $customer->name; 
			if(count($messages) == 0){
					$messagess[] = '<div class="d-flex justify-content-start mb-4"><div class="img_cont_msg"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div><div class="msg_cotainer">New Chat From Customer<span class="msg_time"></span></div></div>';
					
			}else{
				foreach ($messages as $message) {
					if($message->user_id != 0){
						$messagess[] = '<div class="d-flex justify-content-end mb-4"><div class="msg_cotainer_send"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div><div class="msg_cotainer">'.$message->message.'<span class="msg_time">'.\Carbon\Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span></div></div>';
					}else{
						$messagess[] = '<div class="d-flex justify-content-start mb-4"><div class="img_cont_msg"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div><div class="msg_cotainer">'.$message->message.'<span class="msg_time">'.\Carbon\Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span></div></div>';
					}
				}

			}
			
			$count = CustomerLiveChat::where('seen',0)->count();
			return response()->json([
						'status' => 'success',
						'data' => array('id' => $chatId ,'count' => $count, 'message' => $messagess , 'name' => $name),
        			]);
		}else{
			return response()->json([
            			'status' => 'errors'
        			]);
		}
	}
	
	public function getUserList(){
		$liveChatCustomers = CustomerLiveChat::orderBy('seen','asc')->orderBy('status','desc')->get();

		foreach($liveChatCustomers as $liveChatCustomer){
			$customer = Customer::where('id',$liveChatCustomer->customer_id)->first();
			if($liveChatCustomer->status == 0){
				$customers[] = '<li onclick="getChats('.$customer->id.')" id="user'.$customer->id.'"><div class="d-flex bd-highlight"><div class="img_cont"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img"><span class="online_icon offline"></span>
								</div><div class="user_info"><span>'.$customer->name.'</span><p>'.$customer->name.' is offline</p></div></div></li>';
			}elseif($liveChatCustomer->status == 1 && $liveChatCustomer->seen == 0){
				$customers[] = '<li onclick="getChats('.$customer->id.')" id="user'.$customer->id.'"><div class="d-flex bd-highlight"><div class="img_cont"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img"><span class="online_icon"></span>
								</div><div class="user_info"><span>'.$customer->name.'</span><p>'.$customer->name.' is online</p></div><span class="new_message_icon"></span></div></li>';
			}else{
				$customers[] = '<li onclick="getChats('.$customer->id.')" id="user'.$customer->id.'"><div class="d-flex bd-highlight"><div class="img_cont"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img"><span class="online_icon"></span>
								</div><div class="user_info"><span>'.$customer->name.'</span><p>'.$customer->name.' is online</p></div></div></li>';
			}
		}

		//Getting chat counts 
		$count = CustomerLiveChat::where('seen',0)->count();
		
		return response()->json([
						'status' => 'success',
						'data' => array('count' => $count, 'message' => $customers),
        			]);
		
	}


	//Upload FIle COde 
	// public function sendImage($request){
	// 	$uploadedFile = $request->file('file');
	// 	$filename = $uploadedFile->getPathname().'/'.$uploadedFile->getClientOriginalName();
	// 	$target_url = 'https://api.livechatinc.com/v3.1/agent/action/upload_file';
	// 	$cFile = curl_file_create($uploadedFile);
	// 	$post = array('file'=> $cFile);
	// 	$ch = curl_init();
		
	// 	curl_setopt($ch, CURLOPT_URL,$target_url);
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	// 	curl_setopt($ch, CURLOPT_POST,1);
	// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	// 	curl_setopt($ch, CURLOPT_HTTPHEADER,  array(
	// 			"Authorization: Basic NTYwNzZkODktZjJiZi00NjUxLTgwMGQtNzE5YmEyNTYwOWM5OmRhbDpUQ3EwY2FZYVRrMndCTHJ3dTgtaG13",
	// 			"Content-Type: multipart/form-data",));
	// 	$response = curl_exec($ch);
	// 	$err = curl_error($ch);
	// 	curl_close ($ch);
	// 	if ($err) {
	// 			return false;
	// 		} else {
	// 			$response = json_decode($response);
	// 			if(isset($response->error)){
	// 				return false;
	// 			}else{
	// 				return $response->url;
	// 			}
	// 		}	

		
		
	// }
}