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
        $search = request("search");

        
        $pendingApprovalMsg = ChatMessage::join("chatbot_replies as cr","cr.chat_id","chat_messages.id")
        ->join("customers as c","c.id","chat_messages.customer_id");
        
        if(!empty($search)) {
             $pendingApprovalMsg = $pendingApprovalMsg->where(function($q) use($search) {
                $q->where("cr.question","like","%".$search."%")
                ->orWhere("c.name","Like","%".$search."%")
                ->orWhere("chat_messages.message","like","%".$search."%");
             });
        }

        $pendingApprovalMsg = $pendingApprovalMsg->where("status",ChatMessage::CHAT_AUTO_WATSON_REPLY)
        ->where("chat_messages.customer_id", ">", 0)
        ->select(["chat_messages.*","cr.chat_id","cr.question","c.name as customer_name"])
        ->latest()
        ->paginate(20);

        $page = $pendingApprovalMsg->currentPage();

        if($request->ajax()) {
            $tml  = (string)view("chatbot::message.partial.list",compact('pendingApprovalMsg' ,'page')); 
            return response()->json(["code" => 200 , "tpl" => $tml, "page" => $page]);
        }

        return view("chatbot::message.index",compact('pendingApprovalMsg' ,'page'));
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

    /**
     * [removeImages description]
     * @return [type] [description]
     * 
     */
    public function removeImages(Request $request)
    {
      $deleteImages = $request->get("delete_images",[]);

      if(!empty($deleteImages)) {
        foreach($deleteImages as $image) {
            list($mediableId,$mediaId) = explode("_",$image);
            if(!empty($mediaId) && !empty($mediableId)) {
               \Db::statement("delete from mediables where mediable_id = ? and media_id = ? limit 1",[$mediableId,$mediaId]);
            }
        }
      }

      return response()->json(["code" => 200 , "data" => [], "message" => "Image has been removed now"]);
      
    }


    public function attachImages(Request $request)
    {
       $id = $request->get("id",0);

       if($id > 0) {
          // find the chat message
          $chatMessages = ChatMessage::where("id",$id)->first();
          
          if($chatMessages) {
             $chatWatsonReply = $chatMessages->chatBotReply;
             if($chatWatsonReply) {
                // now update the
                $reply = json_decode($chatWatsonReply->reply,true);
                if(!empty($reply)) {
                  
                }
             }
          }
       }

    }

}
