<?php

namespace Modules\ChatBot\Http\Controllers;

use App\Library\Watson\Model as WatsonManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\ChatMessage;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        
        $pendingApprovalMsg = ChatMessage::join("chatbot_replies as cr","cr.chat_id","chat_messages.id")
        ->where("status",ChatMessage::CHAT_AUTO_WATSON_REPLY)
        ->select(["chat_messages.*","cr.chat_id","cr.question"])
        ->latest()
        ->paginate(20);

        return view("chatbot::message.index",compact('pendingApprovalMsg'));
    }

    public function approve() 
    {
    	$id = request("id");
    	
    	if($id > 0) {
    		
    		$myRequest = new Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add(['messageId' => $id]);

            app('App\Http\Controllers\WhatsAppController')->approveMessage('customer', $myRequest);
    	}

    	return response()->json(["code" => 200, "message" => "Messsage Send Successfully"]);

    }

}
