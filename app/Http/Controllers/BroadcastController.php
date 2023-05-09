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

        if (@$inputs['name']) {
            $data->where('name', 'like', '%' . $inputs['name'] . '%');
        }

        if (@$inputs['order']) {
            $data->orderBy('id', $inputs['order']);
        } else {
            $data->latest();
        }

        $data = $data->paginate(15);

        return view('broadcast-messages.index', compact('data', 'inputs', 'suppliers', 'customers', 'vendors'));
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
        $lists = \App\BroadcastMessageNumber::with(['customer', 'vendor', 'supplier'])->where('broadcast_message_id', $id)->orderBy('id', 'DESC')->get();

        return response()->json(['code' => 200, 'data' => $lists]);
    }

    public function sendMessage(Request $request)
    {
        // return $request->all();
        $data = \App\BroadcastMessageNumber::where(['broadcast_message_id' => $request->id])->orderBy('id', 'desc')->groupBy('type_id')->get();
        $isEmail = $request->is_email;
        $params = [];
        $message = [];
        //Create broadcast
        //$broadcast = \App\BroadcastMessage::create(['name'=>$request->name]);
        $BroadcastDetails = \App\BroadcastDetails::create(['broadcast_message_id' => $request->id, 'name' => $request->name, 'message' => $request->message]);
        if (count($data)) {
            foreach ($data as $key => $item) {
                if ($item->type == 'App\Http\Controllers\App\Vendor') {
                    //Vendor
                    $message = [
                        'type_id' => $item->type_id,
                        'type' => App\Vendor::class,
                        //'broadcast_message_id' => $broadcast->id,
                        'broadcast_message_id' => $request->id,
                    ];
                    $broadcastnumber = \App\BroadcastMessageNumber::create($message);

                    $params = [
                        'vendor_id' => $item->type_id,
                        'number' => null,
                        'message' => $request->message,
                        'user_id' => \Auth::id(),
                        'status' => 2,
                        'approved' => 1,
                        'is_queue' => 0,
                        'is_email' => $isEmail,
                        'broadcast_numbers_id' => $broadcastnumber->id,
                    ];
                    $chat_message = \App\ChatMessage::create($params);

                    $approveRequest = new Request();
                    $approveRequest->setMethod('GET');
                    $approveRequest->request->add(['messageId' => $chat_message->id, 'subject' => $request->name]);

                    app(\App\Http\Controllers\WhatsAppController::class)->approveMessage('vendor', $approveRequest, $chat_message->id);
                } elseif ($item->type == 'App\Http\Controllers\App\Supplier') {
                    //Supplier
                    $message = [
                        'type_id' => $item->type_id,
                        'type' => App\Supplier::class,
                        //'broadcast_message_id' => $broadcast->id,
                        'broadcast_message_id' => $request->id,
                    ];
                    $broadcastnumber = \App\BroadcastMessageNumber::create($message);

                    $params = [
                        'supplier_id' => $item->type_id,
                        'number' => null,
                        'message' => $request->message,
                        'user_id' => \Auth::id(),
                        'status' => 1,
                        'is_email' => $isEmail,
                        'broadcast_numbers_id' => $broadcastnumber->id,
                    ];
                    $chat_message = \App\ChatMessage::create($params);

                    $myRequest = new Request();
                    $myRequest->setMethod('POST');
                    $myRequest->request->add(['messageId' => $chat_message->id, 'subject' => $request->name]);
                    app(\App\Http\Controllers\WhatsAppController::class)->approveMessage('supplier', $myRequest, $chat_message->id);
                } else {
                    //Customer
                    $sendingData = [];

                    $message = [
                        'type_id' => $item->type_id,
                        'type' => ErpCustomer::class,
                        //'broadcast_message_id' => $broadcast->id,
                        'broadcast_message_id' => $request->id,
                    ];
                    $broadcastnumber = \App\BroadcastMessageNumber::create($message);

                    $params = [
                        'sending_time' => $request->get('sending_time', ''),
                        'user_id' => \Auth::id(),
                        'message' => $request->message,
                        'phone' => null,
                        'type' => 'message_all',
                        'data' => json_encode($sendingData),
                        'group_id' => '',
                        'is_email' => $isEmail,
                        'broadcast_numbers_id' => $broadcastnumber->id,
                    ];
                    $chat_message = \App\ChatMessage::create($params);
                    $custRequest = new Request();
                    $custRequest->setMethod('POST');
                    $custRequest->request->add(['messageId' => $chat_message->id, 'subject' => $request->name]);
                    app(\App\Http\Controllers\WhatsAppController::class)->approveMessage('customer', $custRequest, $chat_message->id);
                }
            }
        }
        // return $params;

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Message sent successfully']);
    }

    public function sendType(Request $request)
    {
        if ($request->all()) {
            if (count($request->values)) {
                foreach ($request->values as $_value) {
                    if ($request->type == 'vendor') {
                        $message = [
                            'type_id' => $_value,
                            'type' => App\Vendor::class,
                            'broadcast_message_id' => $request->id,
                        ];
                        $broadcastnumber = \App\BroadcastMessageNumber::create($message);
                    } elseif ($request->type == 'supplier') {
                        $message = [
                            'type_id' => $_value,
                            'type' => App\Supplier::class,
                            'broadcast_message_id' => $request->id,
                        ];
                        $broadcastnumber = \App\BroadcastMessageNumber::create($message);
                    } else {
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

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Data added successfully']);
    }

    /**
     * This function user for get the broadcast user group list
     *
     * @return JsonResponse
     */
    public function getSendType(Request $request)
    {
        try {
            $broadData = \App\BroadcastMessageNumber::with(['customer', 'vendor', 'supplier'])->where(['broadcast_message_id' => $request->id])->orderBy('id', 'desc')->groupBy('type_id')->get();

            return response()->json(['code' => 200, 'data' => $broadData, 'message' => 'Data Listed successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function resendMessage(Request $request)
    {
        if ($request['is_last'] == 1) {
            $data = \App\BroadcastMessageNumber::where(['broadcast_message_id' => $request->id])->get();
        } else {
            $data = \App\BroadcastMessageNumber::where(['id' => $request->id])->get();
        }
        $params = [];
        $message = [];

        if (count($data)) {
            foreach ($data as $key => $item) {
                if ($item->type == 'App\Http\Controllers\App\Vendor') {
                    if ($request['is_last'] == 1) {
                        $message_data = \App\ChatMessage::where('vendor_id', $item->type_id)->latest()->first();
                    } else {
                        $message_data = \App\ChatMessage::where('broadcast_numbers_id', $item->id)->first();
                    }
                    //Vendor
                    $message = [
                        'type_id' => $item->type_id,
                        'type' => App\Vendor::class,
                        //'broadcast_message_id' => $broadcast->id,
                        'broadcast_message_id' => $request->id,
                    ];
                    $broadcastnumber = \App\BroadcastMessageNumber::create($message);

                    $params = [
                        'vendor_id' => $item->type_id,
                        'number' => null,
                        'message' => $message_data->message,
                        'user_id' => \Auth::id(),
                        'status' => 2,
                        'approved' => 1,
                        'is_queue' => 0,
                        'broadcast_numbers_id' => $broadcastnumber->id,
                    ];
                    $chat_message = \App\ChatMessage::create($params);

                    $BroadcastDetails = \App\BroadcastDetails::create(['broadcast_message_id' => $request->id, 'name' => $request->name, 'message' => $request->message]);

                    $approveRequest = new Request();
                    $approveRequest->setMethod('GET');
                    $approveRequest->request->add(['messageId' => $chat_message->id]);

                    app(\App\Http\Controllers\WhatsAppController::class)->approveMessage('vendor', $approveRequest);
                } elseif ($item->type == 'App\Http\Controllers\App\Supplier') {
                    if ($request['is_last'] == 1) {
                        $message_data = \App\ChatMessage::where('supplier_id', $item->type_id)->latest()->first();
                    } else {
                        $message_data = \App\ChatMessage::where('broadcast_numbers_id', $item->id)->first();
                    }
                    //Supplier
                    $message = [
                        'type_id' => $item->type_id,
                        'type' => App\Supplier::class,
                        //'broadcast_message_id' => $broadcast->id,
                        'broadcast_message_id' => $request->id,
                    ];
                    $broadcastnumber = \App\BroadcastMessageNumber::create($message);

                    $params = [
                        'supplier_id' => $item->type_id,
                        'number' => null,
                        'message' => $message_data->message,
                        'user_id' => \Auth::id(),
                        'status' => 1,
                        'broadcast_numbers_id' => $broadcastnumber->id,
                    ];
                    $chat_message = \App\ChatMessage::create($params);

                    $BroadcastDetails = \App\BroadcastDetails::create(['broadcast_message_id' => $request->id, 'name' => $request->name, 'message' => $request->message]);

                    $myRequest = new Request();
                    $myRequest->setMethod('POST');
                    $myRequest->request->add(['messageId' => $chat_message->id]);
                    app(\App\Http\Controllers\WhatsAppController::class)->approveMessage('supplier', $myRequest);
                } else {
                    //Customer
                    $sendingData = [];
                    $message = [
                        'type_id' => $item->type_id,
                        'type' => ErpCustomer::class,
                        //'broadcast_message_id' => $broadcast->id,
                        'broadcast_message_id' => $request->id,
                    ];
                    $broadcastnumber = \App\BroadcastMessageNumber::create($message);

                    $BroadcastDetails = \App\BroadcastDetails::create(['broadcast_message_id' => $request->id, 'name' => $request->name, 'message' => $request->message]);

                    $params = [
                        'sending_time' => $request->get('sending_time', ''),
                        'user_id' => \Auth::id(),
                        'phone' => null,
                        'type' => 'message_all',
                        'data' => json_encode($sendingData),
                        'group_id' => '',
                        'broadcast_numbers_id' => $broadcastnumber->id,
                    ];
                }
            }
        }
        // return $params;

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Message sent successfully']);
    }

    public function showMessage(Request $request)
    {
        $massage = \App\BroadcastDetails::where(['broadcast_message_id' => $request->id])->get();
        if (count($massage)) {
            return response()->json(['code' => 200, 'data' => $massage]);
        } else {
            $lists_item = [];

            return response()->json(['code' => 300, 'data' => $lists_item]);
        }
    }
}
