<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use seo2websites\ErpCustomer\ErpCustomer;

class BroadcastController extends Controller
{

    public function index(Request $request)
    {
        $inputs = $request->input();
        $data = \App\BroadcastMessage::with('numbers');

        //Suppliers
        $suppliers = \App\Supplier::all();
        //Vendors
        $vendors = \App\Vendor::all();
        //Customers
        $customers = \App\Customer::all();
        
        if(@$inputs['name']){
            $data->where('name','like','%'.$inputs['name'].'%');
        }

        if(@$inputs['order']){
            $data->orderBy('id',$inputs['order']);
        }else{
            $data->latest();
        }

        $data = $data->paginate(15);

        return view('broadcast-messages.index', compact('data','inputs','suppliers','customers','vendors'));
    }

    public function deleteMessage(Request $request)
    {
        $ID = $request->id;
        $deleted = \App\BroadcastMessage::where('id', $ID)->delete();

        return response()->json(['code' => 200, 'message' => 'Message deleted successfully']);
    }

    public function deleteType(Request $request)
    {
        $ID = $request->id;
        $deleted = \App\BroadcastMessageNumber::where('id', $ID)->delete();

        return response()->json(['code' => 200, 'message' => 'Type deleted successfully']);
    }

    public function messagePreviewNumbers(Request $request)
    {
        $id = $request->id;
        $lists = \App\BroadcastMessageNumber::with(['customer','vendor','supplier'])->where('broadcast_message_id', $id)->orderBy('id', 'DESC')->get();

        return response()->json(["code" => 200, "data" => $lists]);
    }

    public function sendMessage(Request $request)
    {
        // return $request->all();
        $data = \App\BroadcastMessageNumber::where(['broadcast_message_id'=>$request->id])->get();
        $params = [];
        $message = [];
        //Create broadcast
        $broadcast = \App\BroadcastMessage::create(['name'=>$request->name]);
        if (count($data)) {
            foreach ($data as $key => $item) {
                if($item->type == 'App\Http\Controllers\App\Vendor'){
                    //Vendor
                    $params = [
                        'vendor_id' => $item->type_id,
                        'number'    => null,
                        'message'   => $request->message,
                        'user_id'   => \Auth::id(),
                        'status'    => 2,
                        'approved'  => 1,
                        'is_queue'  => 0,
                    ];

                    $message = [
                        'type_id' => $item->type_id,
                        'type' => App\Vendor::class,
                        'broadcast_message_id' => $broadcast->id,
                    ];

                    $chat_message = \App\ChatMessage::create($params);
                    $broadcastnumber = \App\BroadcastMessageNumber::create($message);
                    $approveRequest = new Request();
                    $approveRequest->setMethod('GET');
                    $approveRequest->request->add(['messageId' => $chat_message->id]);

                    app('App\Http\Controllers\WhatsAppController')->approveMessage("vendor", $approveRequest);

                }elseif($item->type == 'App\Http\Controllers\App\Supplier'){

                    //Supplier
                    $params = [
                        'supplier_id' => $item->type_id,
                        'number' => null,
                        'message' => $request->message,
                        'user_id' => \Auth::id(),
                        'status' => 1,
                    ];
                    $message = [
                        'type_id' => $item->type_id,
                        'type' => App\Supplier::class,
                        'broadcast_message_id' => $broadcast->id,
                    ];
                    $broadcastnumber = \App\BroadcastMessageNumber::create($message);
                    $chat_message = \App\ChatMessage::create($params);
                    $myRequest    = new Request();
                    $myRequest->setMethod('POST');
                    $myRequest->request->add(['messageId' => $chat_message->id]);
                    app('App\Http\Controllers\WhatsAppController')->approveMessage('supplier', $myRequest);

                }else{

                    //Customer
                    $sendingData = [];
                    $params = [
                        'sending_time' => $request->get('sending_time', ''),
                        'user_id' => \Auth::id(),
                        'phone' => null,
                        'type' => 'message_all',
                        'data' => json_encode($sendingData),
                        'group_id' => '',
                    ];
                    $message = [
                        'type_id' => $item->type_id,
                        'type' => ErpCustomer::class,
                        'broadcast_message_id' => $broadcast->id,
                    ];
                    $broadcastnumber = \App\BroadcastMessageNumber::create($message);

                }
            }
        }
        // return $params;

        return response()->json(["code" => 200, "data" => [], "message" => "Message sent successfully"]);
    }

    public function sendType(Request $request)
    {
        if ($request->all()) {
            if(count($request->values)){
                    foreach($request->values as $_value){
                        if($request->type == 'vendor'){
                            $message = [
                                'type_id' => $_value,
                                'type' => App\Vendor::class,
                                'broadcast_message_id' => $request->id,
                            ];
                            $broadcastnumber = \App\BroadcastMessageNumber::create($message);
                        }elseif($request->type == 'supplier'){
                            $message = [
                                'type_id' => $_value,
                                'type' => App\Supplier::class,
                                'broadcast_message_id' => $request->id,
                            ];
                            $broadcastnumber = \App\BroadcastMessageNumber::create($message);
                        }else{
                            $message = [
                                'type_id' => $_value,
                                'type' => ErpCustomer::class,
                                'broadcast_message_id' => $request->id,
                            ];
                            $broadcastnumber = \App\BroadcastMessageNumber::create($message);
                        }
                    }
                }

            }
        return response()->json(["code" => 200, "data" => [], "message" => "Data added successfully"]);
    }

}
