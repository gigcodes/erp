<?php

namespace App\Http\Controllers;

use App\LogRequest;
use App\Models\SocialMessages;
use App\SocialContact;
use App\SocialWebhookLog;
use Illuminate\Http\Request;

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
        try {
            $contact = SocialContact::with('socialConfig')->findOrFail($request->contactId);
            $input = $request->input;
            $data['recipient']['id'] = $contact->account_id;
            $data['message']['text'] = $input;
            $pageToken = $contact->socialConfig->page_token;
            $url = "https://graph.facebook.com/v12.0/me/messages?access_token={$pageToken}";
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);

            $curl = curl_init();

            SocialWebhookLog::log(SocialWebhookLog::INFO, 'Send message request', ['data' => $data]);

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                ],
            ]);

            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'POST', json_encode($data), json_decode($response), $httpcode, \App\Http\Controllers\SocialAccountController::class, 'sendMessage');
            curl_close($curl);

            SocialWebhookLog::log(SocialWebhookLog::INFO, 'Send message response', ['response' => $response, 'data' => $data]);

            if ($httpcode == 200) {
                SocialWebhookLog::log(SocialWebhookLog::INFO, 'Message sent successfully', ['response' => $response, 'data' => $data]);

                return response()->json([
                    'message' => 'Message sent successfully',
                ]);
            } else {
                $response = json_decode($response, true);
                SocialWebhookLog::log(SocialWebhookLog::INFO, 'Message not send', ['error' => $response['error']['message'], 'data' => $data]);

                return response()->json([
                    'message' => $response['error']['message'],
                ], $httpcode);
            }
        } catch (\Exception $e) {
            SocialWebhookLog::log(SocialWebhookLog::INFO, 'Message not send', ['error' => $e->getMessage(), 'data' => $data]);

            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
