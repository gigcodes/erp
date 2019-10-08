<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Vendor;
use Illuminate\Http\Request;

class ChatMessagesController extends Controller
{
    /**
     * Load more messages from chat_messages
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadMoreMessages(Request $request)
    {
        // Set limit of messages
        $limit = request()->get("limit", 3);

        // Get object (customer, vendor, etc.)
        switch ( $request->object ) {
            case 'vendor':
                $object = Vendor::find($request->object_id);
                break;
            case 'customer':
                $object = Customer::find($request->object_id);
                break;
            default:
                $object = Customer::find($request->customer_id);
        }

        // Get chat messages
        $chatMessages = $object->whatsappAll()->where("message", "!=", "")->skip(1)->take($limit)->get();

        // Set empty array with messages
        $messages = [];

        // Loop over ChatMessages
        foreach ($chatMessages as $chatMessage) {
            $messages[] = ($chatMessage->number == NULL ? '*OUT*' : '*IN*' ) . $chatMessage->message;
        }

        // Return JSON
        return response()->json([
            'messages' => $messages
        ]);
    }
}
