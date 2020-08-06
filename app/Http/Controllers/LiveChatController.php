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
use App\Helpers\TranslationHelper;
use App\Library\Watson\Model as WatsonManager;


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
				
				// if($customer == '' && $customer == null && $phone != ''){
				// 	//$customer = Customer::where('phone',$phone)->first();
				// }	

				//Save Customer
				if($customer == null && $customer == ''){
					$customer = new Customer;
					$customer->name = $name;
					$customer->email = $email;
					$customer->phone = null;
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
					
					$customerDetails = Customer::find($customerLiveChat->customer_id);
	                $language = $customerDetails->language;
	                if($language !=null)
	                {
	                    $result = TranslationHelper::translate($language, 'en', $message);
	                    $message = $result.' -- '.$message;
	                }
					
					if($author_id == 'buying@amourint.com'){
						$messageStatus = 2;
					}else{
						$messageStatus = 9;
					}
					$params = [
                    	'unique_id' => $chatDetails->chat_id,
                    	'message' => $message,
                    	'customer_id' => $customerLiveChat->customer_id,
                    	'approved' => 1,
                    	'status' => $messageStatus,
						'is_delivered' => 1,
						'user_id' => $userID,
						'message_application_id' => 2,
					];
					
					// Create chat message
                	$chatMessage = ChatMessage::create($params);
                	// if customer found then send reply for it
                	if (!empty($customerDetails) && $message != '') {
                        WatsonManager::sendMessage($customerDetails,$message);
                    }
					
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

					if($author_id == 'buying@amourint.com'){
						$messageStatus = 2;
					}else{
						$messageStatus = 9;
					}

					//creating message
					$params = [
                    	'unique_id' => $chatDetails->chat_id,
                    	'customer_id' => $customerLiveChat->customer_id,
                    	'approved' => 1,
                    	'status' => $messageStatus,
						'is_delivered' => 1,
						'user_id' => $userID,
						'message_application_id' => 2,
					];
					
					$from = 'livechat';
					// Create chat message
					$chatMessage = ChatMessage::create($params);

					$numberPath = date('d');
					$url = $chatDetails->event->url;
					try {
						$jpg = \Image::make($url)->encode('jpg');
						$filename = $chatDetails->event->name;
                    	$media = MediaUploader::fromString($jpg)->toDirectory('/chat-messages/' . $numberPath)->useFilename($filename)->upload();
                    	$chatMessage->attachMedia($media, config('constants.media_tags'));
					} catch (\Exception $e) {
						$file = @file_get_contents($url);
                        if (!empty($file)) {
                        	$filename = $chatDetails->event->name;
                            $media = MediaUploader::fromString($file)->toDirectory('/chat-messages/' . $numberPath)->useFilename($filename)->upload();
                            $chatMessage->attachMedia($media, config('constants.media_tags'));
                        }
					}
					
					
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
				try {
					$websiteURL = self::getDomain($chat->users[0]->last_visit->last_pages[0]->url);
				} catch (\Exception $e) {
					$websiteURL = '';
				}
				//dd($websiteURL);
				$customer = Customer::where('email',$userEmail)->first();
				
				if($customer != '' && $customer != null){
					//Find if its has ID
					$chatID = CustomerLiveChat::where('customer_id',$customer->id)->where('thread',$chatId)->first();
					if($chatID == null && $chatID == ''){

						//check if only thread exist and make it null
						$onlyThreadCheck = CustomerLiveChat::where('thread',$chatId)->first();
						if($onlyThreadCheck){
							$onlyThreadCheck->thread = null;
							$chatID->seen = 1;
							$onlyThreadCheck->save();	
						}

						$customerChatId = new CustomerLiveChat;
						$customerChatId->customer_id = $customer->id;
						$customerChatId->thread = $chatId;
						$customerChatId->status = 1;
						$customerChatId->seen = 0;
						$customerChatId->website = $websiteURL;
						$customerChatId->save();
					}else{
						$chatID->customer_id = $customer->id;
						$chatID->thread = $chatId;
						$chatID->status = 1;
						$chatID->website = $websiteURL;
						$chatID->seen = 0;
						$chatID->update();
					}
				}else{

					//check if only thread exist and make it null
					$onlyThreadCheck = CustomerLiveChat::where('thread',$chatId)->first();
					if($onlyThreadCheck){
						$onlyThreadCheck->thread = null;
						$chatID->seen = 1;
						$onlyThreadCheck->save();	
					}

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
					$customerChatId->website = $websiteURL;
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
			
		    
			$chatId = $request->id;
			$message = $request->message;
			$customerDetails = Customer::find($chatId);
            $language = $customerDetails->language;
            if($language !=null)
            {
                $message = TranslationHelper::translate('en', $language, $message);
            }
			
			//Get Thread ID From Customer Live Chat
			$customer = CustomerLiveChat::where('customer_id',$chatId)->first();
			
			if($customer != '' && $customer != null){
				$thread = $customer->thread;
				
			}else{
				return response()->json([
            	'status' => 'errors'
        		]);
			}
			//dd($thread);
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
				"Authorization: Bearer ".\Cache::get('key')."",
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


	public function uploadFileToLiveChat($image)
	{
		//Save file to path 
		//send path to Live chat
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://api.livechatinc.com/v3.2/agent/action/upload_file",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => array('file'=> new CURLFILE('/Users/satyamtripathi/PhpstormProjects/untitled/images/1592232591.png')),
		CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer ".\Cache::get('key')."",
				"Content-Type: application/json",
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		echo $response;
	}


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

		$threadId = $customer->thread;

		$messages = ChatMessage::where('customer_id',$chatId)->where('message_application_id',2)->get();
		//getting customer name from chat
		$customer = Customer::findorfail($chatId);
		$name = $customer->name;
		
		$customerInfo = $this->getLiveChatIncCustomer($customer->email, 'raw');
		if(!$customerInfo){
			$customerInfo = '';
		}

		$customerInital = substr($name, 0, 1);
		if(count($messages) != 0){
			foreach ($messages as $message) {
			if($message->user_id != 0){
				// Finding Agent 
				$agent = User::where('email', $message->user_id)->first();
				$agentInital = substr($agent->name, 0, 1);

				$messagess[] = '<div class="d-flex justify-content-end mb-4"><div class="rounded-circle user_inital">'.$agentInital.'</div><div class="msg_cotainer">'.$message->message.'<span class="msg_time">'.\Carbon\Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span></div></div>'; //<div class="msg_cotainer_send"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div>
			}else{
				$messagess[] = '<div class="d-flex justify-content-start mb-4"><div class="rounded-circle user_inital">'.$customerInital.'</div><div class="msg_cotainer">'.$message->message.'<span class="msg_time">'.\Carbon\Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span></div></div>'; //<div class="img_cont_msg"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div>
			}
			}

		}
		
		if(!isset($messagess)){
				$messagess[] = '<div class="d-flex justify-content-end mb-4"><div class="rounded-circle user_inital">'.$customerInital.'</div><div class="msg_cotainer">New Customer For Chat<span class="msg_time">'.\Carbon\Carbon::createFromTimeStamp(strtotime(now()))->diffForHumans().'</span></div></div>'; //<div class="msg_cotainer_send"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div>
		}

		$count = CustomerLiveChat::where('seen',0)->count();
		
		return response()->json([
						'status' => 'success',
						'data' => array('id' => $chatId ,'count' => $count, 'message' => $messagess , 'name' => $name, 'customerInfo' => $customerInfo, 'threadId' => $threadId, 'customerInital' => $customerInital),
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
			$customerInital = substr($name, 0, 1);
			if(count($messages) == 0){
					$messagess[] = '<div class="d-flex justify-content-start mb-4"><div class="rounded-circle user_inital">'.$customerInital.'</div><div class="msg_cotainer">New Chat From Customer<span class="msg_time"></span></div></div>'; //<div class="img_cont_msg"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div>
					
			}else{
				foreach ($messages as $message) {

					if($message->user_id != 0){
						// Finding Agent 
						$agent = User::where('email', $message->user_id)->first();
						$agentInital = substr($agent->name, 0, 1);

						if ($message->hasMedia(config('constants.media_tags'))) {
			                    foreach ($message->getMedia(config('constants.media_tags')) as $image) {
			                    	if($message->status == 2){
			                    		$type = 'end';
	                    			}else{
	                    				$type = 'start';
	                    			}

	                    			$messagess[] = '<div class="d-flex justify-content-'.$type.' mb-4"><div class="rounded-circle user_inital">'.$agentInital.'</div><div class="msg_cotainer"><span class="msg_time">'.\Carbon\Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span></div><div class="msg_cotainer_send"><img src="'.$image->getUrl().'" class="rounded-circle-livechat user_img_msg"></div></div>'; 

								}
                		}else{
                			if($message->status == 2){
			                    $type = 'end';
	                    	}else{
	                    		$type = 'start';
	                    	}
                			$messagess[] = '<div class="d-flex justify-content-'.$type.' mb-4"><div class="rounded-circle user_inital">'.$agentInital.'</div><div class="msg_cotainer">'.$message->message.'<span class="msg_time">'.\Carbon\Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span></div></div>'; //<div class="msg_cotainer_send"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div>

                		}
						

						
					}else{

						if ($message->hasMedia(config('constants.media_tags'))) {
			                    foreach ($message->getMedia(config('constants.media_tags')) as $image) {
			                    	if (strpos($image->getUrl(), 'jpeg') !== false) {
    									$attachment = '<a href="" download><img src="'.$image->getUrl().'" class="rounded-circle-livechat user_img_msg"></a>';
									}else{
										$attachment = '<a href="" download>'.$image->filename.'</a>';
									}
			                    	if($message->status == 2){
					                    $type = 'end';
			                    	}else{
			                    		$type = 'start';
			                    	}

			                    	$messagess[] = '<div class="d-flex justify-content-'.$type.' mb-4"><div class="msg_cotainer"><span class="msg_time">'.\Carbon\Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span></div><div class="msg_cotainer_send">'.$attachment.'</div></div>';
								}
                		}else{
                			if($message->status == 2){
					            $type = 'end';
	                    	}else{
	                    		$type = 'start';
	                    	}
                			$messagess[] = '<div class="d-flex justify-content-'.$type.' mb-4"><div class="rounded-circle-livechat user_inital">'.$customerInital.'</div><div class="msg_cotainer">'.$message->message.'<span class="msg_time">'.\Carbon\Carbon::createFromTimeStamp(strtotime($message->created_at))->diffForHumans().'</span></div></div>'; //<div class="img_cont_msg"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div>
                		}
						
					}
				}

			}
			
			$count = CustomerLiveChat::where('seen',0)->count();
			return response()->json([
						'status' => 'success',
						'data' => array('id' => $chatId ,'count' => $count, 'message' => $messagess , 'name' => $name, 'customerInital' => $customerInital),
        			]);
		}
		else{
			return response()->json([
            			'data' => array('id' => '','count' => 0, 'message' => '' , 'name' => '', 'customerInital' => ''),
        			]);
		}
	}

	public function getLiveChats()
	{
		if(session()->has('chat_customer_id'))
		{
			$chatId = session()->get('chat_customer_id');
			$chat_message = ChatMessage::where('customer_id',$chatId)->where('message_application_id',2)->get();
			//getting customer name from chat
			$customer = Customer::findorfail($chatId);
			$name = $customer->name;
			$customerInital = substr($name, 0, 1);
			if(count($chat_message) == 0){
				$message[] = '<div class="d-flex justify-content-start mb-4"><div class="rounded-circle user_inital">'.$customerInital.'</div><div class="msg_cotainer">New Chat From Customer<span class="msg_time"></span></div></div>'; //<div class="img_cont_msg"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div>
			}else{
				foreach ($chat_message as $chat) {
					if($chat->user_id != 0){
						// Finding Agent
						$agent = User::where('email', $chat->user_id)->first();
						$agentInital = substr($agent->name, 0, 1);

						$message[] = '<div class="d-flex justify-content-end mb-4"><div class="rounded-circle user_inital">'.$agentInital.'</div><div class="msg_cotainer">'.$chat->message.'<span class="msg_time">'.\Carbon\Carbon::createFromTimeStamp(strtotime($chat->created_at))->diffForHumans().'</span></div></div>'; //<div class="msg_cotainer_send"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div>
					}else{
						$message[] = '<div class="d-flex justify-content-start mb-4"><div class="rounded-circle user_inital">'.$customerInital.'</div><div class="msg_cotainer">'.$chat->message.'<span class="msg_time">'.\Carbon\Carbon::createFromTimeStamp(strtotime($chat->created_at))->diffForHumans().'</span></div></div>'; //<div class="img_cont_msg"><img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img_msg"></div>
					}
				}

			}
			$count = CustomerLiveChat::where('seen',0)->count();
			return view('livechat.chatMessages', compact('message', 'name', 'customerInital'));
		}
		else{
			$count = 0; $message = ''; $customerInital = '';
			return view('livechat.chatMessages', compact('id' ,'count', 'message', 'name', 'customerInital'));
		}
	}


	public function getUserList(){
		$liveChatCustomers = CustomerLiveChat::orderBy('seen','asc')->orderBy('status','desc')->get();

		foreach($liveChatCustomers as $liveChatCustomer){
			$customer = Customer::where('id',$liveChatCustomer->customer_id)->first();
			$customerInital = substr($customer->name, 0, 1);
			if($liveChatCustomer->status == 0){
				$customers[] = '<li onclick="getChats('.$customer->id.')" id="user'.$customer->id.'" style="cursor: pointer;"><div class="d-flex bd-highlight"><div class="img_cont"><span class="rounded-circle user_inital">'.$customerInital.'</span><span class="online_icon offline"></span>
								</div><div class="user_info"><span>'.$customer->name.'</span><p style="margin-bottom: 0px;">'.$customer->name.' is offline</p><p style="margin-bottom: 0px;">'.$liveChatCustomer->website.'</p></div></div></li><li>'; //<img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img">
			}elseif($liveChatCustomer->status == 1 && $liveChatCustomer->seen == 0){
				$customers[] = '<li onclick="getChats('.$customer->id.')" id="user'.$customer->id.'" style="cursor: pointer;"><div class="d-flex bd-highlight"><div class="img_cont"><span class="rounded-circle user_inital">'.$customerInital.'</span><span class="online_icon"></span>
								</div><div class="user_info"><span>'.$customer->name.'</span><p style="margin-bottom: 0px;">'.$customer->name.' is online</p><p style="margin-bottom: 0px;">'.$liveChatCustomer->website.'</p></div><span class="new_message_icon"></span></div></li>'; //<img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img">
			}else{
				$customers[] = '<li onclick="getChats('.$customer->id.')" id="user'.$customer->id.'" style="cursor: pointer;"><div class="d-flex bd-highlight"><div class="img_cont"><span class="rounded-circle user_inital">'.$customerInital.'</span><span class="online_icon"></span>
								</div><div class="user_info"><span>'.$customer->name.'</span><p style="margin-bottom: 0px;">'.$customer->name.' is online</p><p style="margin-bottom: 0px;">'.$liveChatCustomer->website.'</p></div></div></li>'; //<img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img">
			}
		}
		if(empty($customers)){
			$customers[] = '<li><div class="d-flex bd-highlight"><div class="img_cont">
								</div><div class="user_info"><span>No User Found</span><p></p></div></div></li>';
		}
		//Getting chat counts 
		$count = CustomerLiveChat::where('seen',0)->count();
		
		return response()->json([
						'status' => 'success',
						'data' => array('count' => $count, 'message' => $customers),
        			]);
		
	}


	public function checkNewChat()
	{
		$count = CustomerLiveChat::where('seen',0)->count();
		return response()->json([
						'status' => 'success',
						'data' => array('count' => $count),
        			]);
	}


	/**
	* function to get customer details from livechatinc
	* https://api.livechatinc.com/v3.1/agent/action/get_customers
	*
	* @param customer's email address
	*   
	* @return - response livechatinc object of customer information. If error return false
	*/
	function getLiveChatIncCustomer($email='', $out='JSON'){
		$threadId = '';
		if($email == '' && session()->has('chat_customer_id')){
			$chatId = session()->get('chat_customer_id');
			$messages = ChatMessage::where('customer_id',$chatId)->where('message_application_id', 2)->get();
			//getting customer name from chat
			$customer = Customer::findorfail($chatId);
			$email = $customer->email;

			$liveChatCustomer = CustomerLiveChat::where('customer_id',$chatId)->first();
			$threadId = $liveChatCustomer->thread;
		}

		$returnVal = '';
		if($email != ''){
			$postURL = 'https://api.livechatinc.com/v3.1/agent/action/get_customers';

			$postData = array('filters' => array('email' => array('values' => array($email))));
			$postData = json_encode($postData);
			
			$returnVal = '';
			$result = self::curlCall($postURL, $postData, 'application/json');
			if($result['err']){
				// echo "ERROR 1:<br>";
				// print_r($result['err']);
				$returnVal = false;
			}
			else{
				$response = json_decode($result['response']);
				if(isset($response->error)){
					// echo "ERROR 2:<br>";
					// print_r($response);				
					$returnVal = false;
				}
				else{
					// echo "SUCSESS:<BR>";
					// print_r($response);
					$returnVal = $response->customers[0];
				}
			}
		}

		if($out == 'JSON'){
			return response()->json(['status' => 'success', 'threadId' => $threadId, 'customerInfo' => $returnVal], 200);
		}
		else{
			return $returnVal;
		}
	}
	
	/**
	* function to upload file/image to liveshatinc
	* upload file to livechatinc using their agent /action/upload_file api which will respond with livechatinc CDN url for file uploaded
	* https://api.livechatinc.com/v3.1/agent/action/upload_file
	*
	* @param request
	*   
	* @return - response livechatinc CDN url for the file. If error return false
	*/
	function uploadFileToLiveChatInc(Request $request){
		//To try with static file from local file, uncomment below
		//$filename = 'delete-red-cross.png';
		//$fileURL = public_path() . '/images/' . $filename;
		$uploadedFile = $request->file('file');
		$mimeType = $uploadedFile->getMimeType();
		$filename = $uploadedFile->getClientOriginalName();

		$postURL = 'https://api.livechatinc.com/v3.1/agent/action/upload_file';

		//echo 'File: ' . $fileURL . ', MType: ' . mime_content_type($fileURL) .'<br>';
		//$postData = array('file' => curl_file_create($fileURL, mime_content_type($fileURL), basename($fileURL)));
		//echo 'File: ' . $filename . ', MType: ' . $mimeType;

		$postData = array('file' => curl_file_create($uploadedFile, $mimeType, $filename));
		
		$result = self::curlCall($postURL, $postData, 'multipart/form-data');
		if($result['err']){
			// echo "ERROR 1:<br>";
			// print_r($result['err']);
			return false;
		}
		else{
			$response = json_decode($result['response']);
			if(isset($response->error)){
				// echo "ERROR 2:<br>";
				// print_r($response);				
				return false;
			}
			else{
				// echo "SUCSESS:<BR>";
				// print_r($response);
				return ['CDNPath' => $response->url, 'filename' => $filename];
			}
		}
	}

	/**
	* curlCall function to make a curl call
	*
	* @param 
	*   URL - url that we need to access and make curl call,
	*   method - curl call method - GET, POST etc
	*   contentType - Content-Type value to set in headers
	*   data - data that has to be sent in curl call. This can be optional if GET
	* @return - response from curl call, array(response, err)
	*/
	function curlCall($URL, $data=false, $contentType=false, $defaultAuthorization=true, $method='POST'){
		$curl = curl_init();

		$curlData = array(
			CURLOPT_URL => $URL,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
		);

		if($method == 'POST'){
			$curlData[CURLOPT_POST] = 1;
		}
		else{
			$curlData[CURLOPT_CUSTOMREQUEST] = $method;
		}
		if($contentType){
			$curlData[CURLOPT_HTTPHEADER] = [];
			if($defaultAuthorization){
				array_push($curlData[CURLOPT_HTTPHEADER], "Authorization: Bearer ".\Cache::get('key')."");
			}
			// $curlData[CURLOPT_HTTPHEADER] = array(
			// 	"Authorization: Basic NTYwNzZkODktZjJiZi00NjUxLTgwMGQtNzE5YmEyNTYwOWM5OmRhbDpUQ3EwY2FZYVRrMndCTHJ3dTgtaG13",
			// 	"Content-Type: " . $contentType
			// );
			array_push($curlData[CURLOPT_HTTPHEADER], "Content-Type: " . $contentType);
		}
		if($data){
			$curlData[CURLOPT_POSTFIELDS] = $data;
		}

		curl_setopt_array($curl, $curlData);
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		return array('response' => $response, 'err' => $err);
	}

	/**
	* CDN URL got after uploading file to livechatinc will expire in 24hrs unless its used in sent_event api
	* send the CDN URL to livechatinc using sent_event api to keep the CDN URL alive
	* https://developers.livechatinc.com/docs/messaging/agent-chat-api/#file
	* https://developers.livechatinc.com/docs/messaging/agent-chat-api/#send-event
	*/
	function sendFileToLiveChatInc(Request $request){
		$chatId = $request->id;
		//Get Thread ID From Customer Live Chat
		$customer = CustomerLiveChat::where('customer_id', $chatId)->first();
		if($customer != '' && $customer != null){
			$thread = $customer->thread;
		}
		else{
			return response()->json(['status' => 'errors', 'errorMsg' => 'Thread not found'], 200);
		}

		$fileUploadResult = self::uploadFileToLiveChatInc($request);

		if(!$fileUploadResult){ //There is some error, we didn't get the CDN file path
			//return false;
			return response()->json(['status' => 'errors', 'errorMsg' => 'Error uploading file'], 200);
		}
		else{
			$fileCDNPath = $fileUploadResult['CDNPath'];
			$filename = $fileUploadResult['filename'];
		}

		$postData = array('chat_id' => $thread, 'event' => array('type' => 'file', 'url' => $fileCDNPath, 'recipients' => 'all',));
		$postData = json_encode($postData);

		$postURL = 'https://api.livechatinc.com/v3.1/agent/action/send_event';

		$result = self::curlCall($postURL, $postData, 'application/json');
		if($result['err']){
			// echo "ERROR 1:<br>";
			// print_r($result['err']);
			//return false;
			return response()->json(['status' => 'errors', 'errorMsg' => $result['err']], 403);
		}
		else{
			$response = json_decode($result['response']);
			if(isset($response->error)){
				// echo "ERROR 2:<br>";
				// print_r($response);				
				return response()->json(['status' => 'errors', $response], 403);
			}
			else{
				// echo "SUCSESS:<BR>";
				// print_r($response);
				//return $response->url;
				return response()->json(['status' => 'success', 'filename' => $filename, 'fileCDNPath' => $fileCDNPath, 'responseData' => $response], 200);
			}
		}
	}

	public static function getDomain($url)
	{
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];
			if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
				return $regs['domain'];
			}
		return false;
	}

	public function saveToken(Request $request)
	{
		if($request->accessToken){
			//dd($request->accessToken);
			$storedCache = \Cache::get('key');
			if($storedCache){
				if($storedCache != $request->accessToken){
					try {
						\Cache::put('key', $request->accessToken, $request->seconds);
					} catch (Exception $e) {
						\Cache::add('key', $request->accessToken, $request->seconds);
					}
				}
			}else{
				try {
						\Cache::put('key', $request->accessToken, $request->seconds);
					} catch (Exception $e) {
						\Cache::add('key', $request->accessToken, $request->seconds);
					}
			}
			//session()->put('livechat_accesstoken', $request->accessToken);
			//\Session::put('livechat_accesstoken', $request->accessToken);
			//$request->session()->put('livechat_accesstoken', $request->accessToken);
			return response()->json(['status' => 'success', 'message' => 'AccessToken saved'], 200);
		}
		return response()->json(['status' => 'error', 'message' => 'AccessToken cannot be saved'], 500);
	}
}