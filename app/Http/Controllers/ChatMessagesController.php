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
        $limit = $request->get("limit", 3);
        $loadAttached = $request->get("load_attached", 0);

        // Get object (customer, vendor, etc.)
        switch ($request->object) {
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
        $chatMessages = $object->whatsappAll()->whereRaw("(message!='' or media_url!='')")->skip(0)->take($limit)->get();

        // Set empty array with messages
        $messages = [];

        // Loop over ChatMessages
        foreach ($chatMessages as $chatMessage) {
            // Create empty media array
            $media = [];

            // Check for media
            if ($loadAttached == 1 && $chatMessage->hasMedia(config('constants.media_tags'))) {
                foreach ($chatMessage->getMedia(config('constants.media_tags')) as $key => $image) {
                    $media[] = $image->getUrl();
                }
            }

            $messages[] = [
                'inout' => $chatMessage->number != $object->phone ? 'out' : 'in',
                'message' => $chatMessage->message,
                'media_url' => $chatMessage->media_url,
                'datetime' => $chatMessage->created_at,
                'media' => is_array($media) ? $media : null
            ];
        }

        // Return JSON
        return response()->json([
            'messages' => $messages
        ]);
    }
}
