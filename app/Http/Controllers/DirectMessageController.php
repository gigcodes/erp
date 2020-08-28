<?php

namespace App\Http\Controllers;

use InstagramAPI\Instagram;
use Illuminate\Http\Request;
use \App\Account;
use \App\Customer;
use \App\InstagramDirectMessages;
use \App\InstagramUsersList;
use \App\InstagramThread;
use Plank\Mediable\Media;
use InstagramAPI\Media\Photo\InstagramPhoto;
Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class DirectMessageController extends Controller
{
    public function index()
    {
    	$threads = InstagramThread::whereNotNull('instagram_user_id')->whereNotNull('account_id')->get();

    	// if ($request->ajax()) {
     //        return response()->json([
     //            'tbody' => view('instagram.direct.data', compact('threads'))->render(),
     //            'links' => (string)$documents->render()
     //        ], 200);
     //    }

    	return view('instagram.direct.index',['threads' => $threads]);
    }


    public function incomingPendingRead(Request $request)
    {
    	$accounts = Account::where('platform','instagram')->whereNotNull('proxy')->get();

    	foreach ($accounts as $account) {

    		try {
                	$instagram = new Instagram();
                    $instagram->setProxy($account->proxy);
				    $instagram->login($account->last_name, $account->password);
				    $this->instagram = $instagram;
                } catch (\Exception $e) {
                    dd($e);
                    echo "ERROR $account->last_name \n";
                    continue;
                }
                //getting inbpx
                $inbox = $this->instagram->direct->getInbox()->asArray();
                //getting inbox

                if (isset($inbox['inbox']['threads'])) {
                	 $incomingThread = $inbox['inbox'];
                	if($incomingThread['unseen_count'] != 0){
	                    $threads = $inbox['inbox']['threads'];
	                    foreach ($threads as $thread) {
	                        $user = $thread['users'];

							//check instagram Users
	                        $userInstagram = InstagramUsersList::where('user_id',$user[0]['pk'])->first();
	                        
	                        if(!$userInstagram){
	                        	$info = $user[0];
	                        	$userInstagram = new InstagramUsersList();
	                        	$userInstagram->username = $info['username'];
		                        $userInstagram->user_id = $user[0]['pk'];
		                        $userInstagram->image_url = $info['profile_pic_url'];
		                        $userInstagram->bio = '';
		                        $userInstagram->rating = 0;
		                        $userInstagram->location_id = 0;
		                        $userInstagram->because_of = 'instagram_dm';
		                        $userInstagram->posts = 0;
		                        $userInstagram->followers = 0;
		                        $userInstagram->following = 0;
		                        $userInstagram->location = '';
		                        $userInstagram->save(); 
			                    
	                        }
	                        
	                        $threadId = self::createThread($userInstagram , $thread , $account->id);

	                        $currentUser = $this->instagram->account_id;
	                        self::createDirectMessage($thread,$threadId,$currentUser);

	                        $account->new_message = 1;
	                		$account->save();
	                        
	                	}
                    }
                }

                //getting pending inbox message
                $inbox = $this->instagram->direct->getPendingInbox()->asArray();
                $incomingThread = $inbox['inbox'];
                if($incomingThread['unseen_count'] != 0){
                	$account->new_message = 1;
                	$account->save();
                		
                }
                
    	}

    	$threads = InstagramThread::whereNotNull('instagram_user_id')->whereNotNull('account_id')->paginate(25);

    	if ($request->ajax()) {
            return response()->json([
                'tbody' => view('instagram.direct.data', compact('threads'))->render(),
                'links' => (string)$threads->render()
            ], 200);
        }

    	return response()->json([
            	'status' => 'success'
        		]);	

    }
    public function getDirectMessagesFromAccounts()
    {
    	$accounts = Account::where('platform','instagram')->whereNotNull('proxy')->where('new_message',1)->get();

    	foreach ($accounts as $account) {

                try {
                	$instagram = new Instagram();
				    $instagram->login($account->last_name, $account->password);
				    $instagram->setProxy($account->proxy);
				    $this->instagram = $instagram;
                } catch (\Exception $e) {
                    dd($e);
                    echo "ERROR $account->last_name \n";
                    continue;
                }

                $inbox = $this->instagram->direct->getInbox()->asArray();
                
                if (isset($inbox['inbox']['threads'])) {
                    $threads = $inbox['inbox']['threads'];
                    foreach ($threads as $thread) {
                        $user = $thread['users'];

						//check instagram Users
                        $userInstagram = InstagramUsersList::where('user_id',$user[0]['pk'])->first();
                        
                        if(!$userInstagram){
                        	$info = $user[0];
                        	$userInstagram = new InstagramUsersList();
                        	$userInstagram->username = $info['username'];
	                        $userInstagram->user_id = $user[0]['pk'];
	                        $userInstagram->image_url = $info['profile_pic_url'];
	                        $userInstagram->bio = '';
	                        $userInstagram->rating = 0;
	                        $userInstagram->location_id = 0;
	                        $userInstagram->because_of = 'instagram_dm';
	                        $userInstagram->posts = 0;
	                        $userInstagram->followers = 0;
	                        $userInstagram->following = 0;
	                        $userInstagram->location = '';
	                        $userInstagram->save(); 
		                    
                        }
                        
                        $threadId = self::createThread($userInstagram , $thread , $account->id);

                        $currentUser = $this->instagram->account_id;
                        self::createDirectMessage($thread,$threadId,$currentUser);

                       
                        

                    }
                }
            }
    }

     /**
     * @param $user
     * @return Customer|void
     */
    private function createDirectMessage($t,$id,$userId)
    {
    	$thread = $this->instagram->direct->getThread($t['thread_id'])->asArray();
    	$thread = $thread['thread'];
    	foreach ($thread['items'] as $chat) {
    		if ($chat['item_type'] == 'text') {
    			$type = 1;
                $text = $chat['text'];
            } else if ($chat['item_type'] == 'like') {
                $text = $chat['like'];
                $type = 2;
            } else if ($chat['item_type'] == 'media') {
            	$type = 3;
                $text = $chat['media']['image_versions2']['candidates'][0]['url'];
            }
            if($chat['user_id'] == $userId){
                $isSent = 1;
            }else{
                $isSent = 0;
            }
           $directMessage = InstagramDirectMessages::where('instagram_thread_id',$id)->where('message',$text)->first();
           if(!$directMessage){
           		$directMessage = new InstagramDirectMessages();
           		$directMessage->instagram_thread_id = $id;
           		$directMessage->message = $text;
           		$directMessage->message_type = $type;
           		$directMessage->sender_id = $chat['user_id'];
           		$directMessage->receiver_id = $userId;
                $directMessage->is_send = $isSent;
           		$directMessage->status = 1;
           		$directMessage->save();
           }
    	}
    }

    private function createThread($userInstagram, $t , $accountId)
    {
    	$thread = InstagramThread::where('thread_id',$t['thread_id'])->first();
    	if(!$thread){
    		$thread = new InstagramThread();
	        $thread->instagram_user_id  = $userInstagram->id;
	        $thread->account_id  = $accountId;
	        $thread->thread_id    = $t['thread_id'];
	        $thread->thread_v2_id = $t['thread_v2_id'];
	        $thread->save();
		}
        
        return $thread->id;

    }

    public function sendMessage(Request $request) {
        
        $thread = InstagramThread::find($request->thread_id);
        $agent = $thread->account;
        $messageType = 1;
        if($agent){

        	$status = $this->sendMessageToInstagramUser($thread->account->last_name, $thread->account->password, $thread->account->proxy, $thread->instagramUser->username, $request->message);
        	
		}
		
        if ($status === false) {
            return response()->json([
                'error'
            ], 413);
        }

        
        $dm = new InstagramDirectMessages();
        $dm->instagram_thread_id = $thread->id;
        $dm->message_type = $messageType;
        $dm->sender_id = $status[1];
        $dm->message = $status[2];
        $dm->receiver_id = $thread->instagramUser->user_id;
        $dm->status = 1;
        $dm->is_send = 1;
        $dm->save();

        //updating account status

        $thread->account->new_message = 0;
        $thread->account->save();

        return response()->json([
            'status' => 'success',
            'receiver_id' => $thread->instagramUser->user_id,
            'sender_id' => $status[1],
            'message' => $status[2]
        ]);

    }


    public function sendImage(Request $request)
    {
        
        if($request->nothing){
            $id = $request->nothing;
            $thread = InstagramThread::find($id);
            $agent = $thread->account;
            $messageType = 1;
            if($agent){
                $images = json_decode($request->get("images"), true);
                if($images){
                    foreach ($images as $image) {
                            $image = Media::find($image);
                            $status = $this->sendFileToInstagramUser($thread->account->last_name, $thread->account->password, $thread->account->proxy, $thread->instagramUser->username, $image);
                        }
                }
                
            }
            
        }
       
       
    }

    private function sendFileToInstagramUser($sender, $password, $proxy , $receiver, $file) {
        $i = new Instagram();

        try {
            $i->setProxy($proxy);
            $i->login($sender, $password);
        } catch (\Exception $exception) {
            return false;
        }

        try {
            $receiver = $i->people->getUserIdForName($receiver);
        } catch (Exception $e) {
            return false;
        }


        //$fileName = Storage::disk('uploads')->putFile('', $file);

        $photo = new InstagramPhoto($file->getAbsolutePath());
       
        $imageInfo = $i->direct->sendPhoto([
            'users' => [
                $receiver
            ]
        ], $photo->getFile());

        return [true, $i->account_id, $file->filename];


    }

    private function sendMessageToInstagramUser($sender, $password, $proxy, $receiver, $message) {


        $i = new Instagram();

        try {
        	$i->setProxy($proxy);
            $i->login($sender, $password);
        } catch (\Exception $exception) {
            return false;
        }


        try {
        	$receiver = $i->people->getUserIdForName($receiver);
        } catch (Exception $e) {
        	return false;
        }
            
        

        try {
            $i->direct->sendText([
                'users' => [
                    $receiver
                ]
            ], $message);
        } catch (\Exception $exception) {
            dd($exception);
        }
        return [true, $i->account_id, $message];

    }

    public function messages(Request $request)
    {
        $id = $request->id;
        $thread = InstagramThread::find($id);
        if($thread){
            $chats = $thread->conversation;
            $html = '<div style="overflow-x:auto;"><input type="text" id="click-to-clipboard-message" class="link" style="position: absolute; left: -5000px;"><table class="table table-bordered"><tbody><tr class="in-background"><tr>';
            foreach ($chats as $chat) {

                if(isset($chat->getRecieverUsername->username)){
                    $receiver = $chat->getRecieverUsername->username;
                }else{
                    $receiver = 'unknown';
                }

                if(isset($chat->getSenderUsername->username)){
                    $sender = $chat->getSenderUsername->username;
                }else{
                    $sender = 'unknown';
                }


                if($chat->message_type == 3){
                    $message = '<img src="'.$chat->message.'" height="200px" width="200px">';
                }else{
                    $message = $chat->message;
                }


                $html .= '<td style="width:5%"><input data-id="{{ $chat->id }}" data-message="" type="checkbox" class="click-to-clipboard"></td><td style="width:45%"><div class="speech-wrapper "><div class="bubble"><div class="txt"><p class="name"></p><p class="message" data-message="">'. $message .'</p></div></div></div></td><td style="width:30%"><a title="Remove" href="javascript:;" class="btn btn-xs btn-secondary ml-1 delete-message" data-id="505729"><i class="fa fa-trash" aria-hidden="true"></i></a><a title="Dialog" href="javascript:;" class="btn btn-xs btn-secondary ml-1 create-dialog"><i class="fa fa-plus" aria-hidden="true"></i></a></td><td style="width:20%"><span class="timestamp" style="color:black; text-transform: capitalize;font-size: 14px;">From '. $sender .' to '. $receiver  .' on '.
                    $chat->created_at.'</span></td></tr><tr class="in-background">';
            }

            $html .= '</tr></tbody></table></div>';

            return response()->json([
            'status' => 'success',
            'messages' => $html
            ]);
            
        }
    }
}
