<?php

namespace Modules\MessageQueue\Http\Controllers;

use App\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class MessageQueueController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('messagequeue::index');
    }

    /**
     * Display a listing of the resource.
     * @return Response Json
     */
    public function records()
    {
        $from  = request("from", "");
        $to    = request("to", "");
        $limit = request("limit", config('erp-customer.pagination'));

        $chatMessage = ChatMessage::join("customers as c", "c.id", "chat_messages.customer_id")
            ->where("is_queue", ">", 0)
            ->where("customer_id", ">", 0);

        if (!empty($from)) {
            $chatMessage = $chatMessage->where("c.whatsapp_number", "like", "%" . $from . "%");
        }

        if (!empty($to)) {
            $chatMessage = $chatMessage->where("c.phone", "like", "%" . $to . "%");
        }

        $chatMessage = $chatMessage->select(["chat_messages.*", "c.phone", "c.whatsapp_number"]);

        $chatMessage = $chatMessage->paginate($limit);

        return response()->json([
            "code"       => 200,
            "data"       => $chatMessage->items(),
            "pagination" => (string) $chatMessage->links(),
        ]);
    }

    public function deleteRecord(Request $request, $id)
    {
        $message = ChatMessage::find($id);

        if (!empty($message)) {
            $message->delete();
            return response()->json(["code" => 200, "message" => "Deleted Successfully"]);
        }

        return response()->json(["code" => 500, "message" => "Sorry no message found in records"]);

    }

    public function actionHandler(Request $request)
    {
        $action = $request->get("action", "");
        $ids    = $request->get("ids", []);

        switch ($action) {
            case 'change_to_broadcast':
                if (!empty($ids) && is_array($ids)) {
                    \DB::update("update chat_messages as cm join customers as c on c.id = cm.customer_id join whatsapp_configs as wc
                    on wc.number = c.broadcast_number set cm.is_queue = wc.id where cm.id in (" . implode(",", $ids) . ");");
                    return response()->json(["code" => 200, "message" => "Deleted Successfully"]);
                }
                break;
            case 'delete_records':

                if (!empty($ids) && is_array($ids)) {
                    ChatMessage::whereIn("id", $ids)->delete();
                    return response()->json(["code" => 200, "message" => "Deleted Successfully"]);
                }

                break;
            case 'delete_all':
                    ChatMessage::where("is_queue",">",0)->delete();
                    return response()->json(["code" => 200, "message" => "Deleted Successfully"]);
                break;    
        }

        return response()->json(["code" => 500, "message" => "Oops, something went wrong"]);

    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('messagequeue::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('messagequeue::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('messagequeue::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
