<?php

namespace Modules\ChatBot\Http\Controllers;

use App\ChatbotCategory;
use App\ChatMessage;
use App\Suggestion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $search = request("search");
        $status = request("status");

        $pendingApprovalMsg = ChatMessage::leftjoin("customers as c", "c.id", "chat_messages.customer_id")
            ->join("vendors as v", "v.id", "chat_messages.vendor_id")
            ->leftJoin("store_websites as sw","sw.id","c.store_website_id")
            ->Join("chatbot_replies as cr", "cr.replied_chat_id", "chat_messages.id")
            ->leftJoin("chat_messages as cm1", "cm1.id", "cr.chat_id");

        if (!empty($search)) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) use ($search) {
                $q->where("cr.question", "like", "%" . $search . "%")->orWhere("cr.answer", "Like", "%" . $search . "%");
            });
        }

        if(isset($status) && $status !== null){
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) use ($status) {
                $q->where("chat_messages.approved", $status);
            });
        }

        $pendingApprovalMsg = $pendingApprovalMsg->where(function($q) {
            $q->where("chat_messages.message","!=", "");
        })->select(["chat_messages.*", "cm1.id as chat_id", "cr.question","cm1.message as answer", "c.name as customer_name","v.name as vendors_name","cr.reply_from","cm1.approved","sw.title as website_title"])
        ->orderBy("chat_messages.id","desc")
        ->paginate(20);
            
        $allCategory = ChatbotCategory::all();
        $allCategoryList = [];
        if (!$allCategory->isEmpty()) {
            foreach ($allCategory as $all) {
                $allCategoryList[] = ["id" => $all->id, "text" => $all->name];
            }
        }
        $page = $pendingApprovalMsg->currentPage();
        if ($request->ajax()) {
            $tml = (string) view("chatbot::message.partial.list", compact('pendingApprovalMsg', 'page','allCategoryList'));
            return response()->json(["code" => 200, "tpl" => $tml, "page" => $page]);
        }

        
//dd($pendingApprovalMsg);
        return view("chatbot::message.index", compact('pendingApprovalMsg', 'page', 'allCategoryList'));
    }

    public function approve()
    {
        $id = request("id");

        if ($id > 0) {

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
        $deleteImages = $request->get("delete_images", []);

        if (!empty($deleteImages)) {
            foreach ($deleteImages as $image) {
                list($mediableId, $mediaId) = explode("_", $image);
                if (!empty($mediaId) && !empty($mediableId)) {
                    \Db::statement("delete from mediables where mediable_id = ? and media_id = ? limit 1", [$mediableId, $mediaId]);
                }
            }
        }

        return response()->json(["code" => 200, "data" => [], "message" => "Image has been removed now"]);

    }

    public function attachImages(Request $request)
    {
        $id = $request->get("chat_id", 0);

        $data   = [];
        $ids    = [];
        $images = [];

        if ($id > 0) {
            // find the chat message
            $chatMessages = ChatMessage::where("id", $id)->first();

            if ($chatMessages) {
                $chatsuggestion = $chatMessages->suggestion;
                if ($chatsuggestion) {
                    $data    = Suggestion::attachMoreProducts($chatsuggestion);
                    $code    = 500;
                    $message = "Sorry no images found!";
                    if (count($data) > 0) {
                        $code    = 200;
                        $message = "More images attached Successfully";
                    }
                    return response()->json(["code" => $code, "data" => $data, "message" => $message]);
                }
            }

            return response()->json(["code" => 200, "data" => [], "message" => "Sorry , There is not avaialble images"]);
        }

        return response()->json(["code" => 500, "data" => [], "message" => "It looks like there is not validate id"]);

    }

    public function forwardToCustomer(Request $request)
    {
        $customer = $request->get("customer");
        $images   = $request->get("images");

        if($customer > 0 && !empty($images)) {

            $params = request()->all();
            $params["user_id"] = \Auth::id();
            $params["is_queue"] = 0;
            $params["status"] = \App\ChatMessage::CHAT_MESSAGE_APPROVED;
            $params["customer_ids"] = is_array($customer) ? $customer : [$customer];
            $groupId = \DB::table('chat_messages')->max('group_id');
            $params["group_id"] = ($groupId > 0) ? $groupId + 1 : 1;
            $params["images"] = $images;
            
            \App\Jobs\SendMessageToCustomer::dispatch($params);

        }

        return response()->json(["code" => 200 , "data" => [], "message" => "Message forward to customer(s)"]);

    }

    public function resendToBot(Request $request)
    {
        $chatId = $request->get("chat_id");

        if(!empty($chatId)) {
            $chatMessage = \App\ChatMessage::find($chatId);
            if($chatMessage) {
                $customer = $chatMessage->customer;
                if($customer) {
                    $params = $chatMessage->getAttributes();
                    \App\Helpers\MessageHelper::whatsAppSend($customer,$chatMessage->message, null , $chatMessage);
                    \App\Helpers\MessageHelper::sendwatson($customer,$chatMessage->message, null , $chatMessage, $params);

                    return response()->json(["code" => 200 , "data" => [] , "message" => "Message sent Successfully"]);
                }
            }
        }

        return response()->json(["code" => 500 , "data" => [] , "message" => "Message not exist in record"]);
    } 

}
