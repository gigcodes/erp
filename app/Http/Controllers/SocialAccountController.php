<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\SocialContact;
use Illuminate\Http\Request;
use App\Models\SocialMessages;

class SocialAccountController extends Controller
{
    /**
     * Show inbox user list
     */
    public function inbox()
    {
        $socialContact = SocialContact::with('socialConfig.storeWebsite', 'messages')->get();

        return view('instagram.inbox', compact('socialContact'));
    }

    /**
     * List Message of specific user
     */
    public function listMessage(Request $request)
    {
        try {
            $contactId = $request->id;
            $messages = SocialMessages::where('social_contact_id', $contactId)->get();
            return response()->json(['messages' => $messages]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Sending Message to Social contact user
     */
    public function sendMessage(Request $request)
    {
        $contact = SocialContact::with('socialConfig')->findOrFail($request->contactId);
        $firstMessage = $contact->messages()->first();
        $message = $request->input;

        $pageInfoParams = [ // endpoint and params for getting page
            'endpoint_path' => $contact->socialConfig->page_id . '/messages',
            'fields' => '',
            'access_token' => $contact->socialConfig->page_token,
            'request_type' => 'POST',
            'data' => [
                'recipient' => [
                    'id' => $firstMessage->from['id'],
                ],
                'message' => [
                    'text' => $message,
                ],
            ],
        ];

        $response = getFacebookResults($pageInfoParams);

        if (! isset($response['data']['error'])) {
            $contact->messages()->create([
                'from' => $firstMessage->to,
                'to' => $firstMessage->from,
                'message' => $message,
                'message_id' => $response['data']['message_id'],
                'created_time' => Carbon::now(),
            ]);

            return response()->json([
                'message' => 'Message sent successfully',
            ]);
        } else {
            return response()->json([
                'message' => 'Unable to sent message',
            ]);
        }
    }
}
