<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\SocialContact;
use Illuminate\Http\Request;
use App\Services\Facebook\FB;
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
        $page_id = $contact->socialConfig->page_id;
        $to = $firstMessage->from['id'] != $page_id ? $firstMessage->from : $firstMessage->to[0];
        $from = $firstMessage->from['id'] == $page_id ? $firstMessage->from : $firstMessage->to[0];

        $message = $request->input;

        $fb = new FB($contact->socialConfig->page_token);

        try {
            $response = $fb->replyFbMessage($contact->socialConfig->page_id, $to['id'], $message);
            $contact->messages()->create([
                'from' => $from,
                'to' => $to,
                'message' => $message,
                'message_id' => $response['data']['message_id'],
                'created_time' => Carbon::now(),
            ]);

            return response()->json([
                'message' => 'Message sent successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to sent message',
            ]);
        }
    }
}
