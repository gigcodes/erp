<?php

namespace App\Http\Controllers;

use App\Social\SocialConfig;
use App\SocialContact;
use App\SocialContactThread;
use Illuminate\Http\Request;

class SocialWebhookController extends Controller
{
    public function inbox()
    {
        $socialContact = SocialContact::with('socialConfig', 'getLatestSocialContactThread')->get();
        return view('instagram.inbox', compact('socialContact'));
    }

    public function listMessage(Request $request)
    {
        try {
            $contactId = $request->id;
            $contact = SocialContact::with('socialConfig', 'socialContactThread')->findOrFail($contactId);
            return response()->json(array('messages' => $contact));
        } catch (\Exception $e) {
            return response()->json(array('error' => $e->getMessage()), 500);
        }
    }

    public function verifyWebhook(Request $request)
    {
        $hub = $request->all();
        $verifyToken = $hub['hub_verify_token'];
        $challange = $hub['hub_challenge'];

        \Log::info("Insagram Webhook (Verify Webhook) => Webhook Verifying.....", ['token' => $verifyToken]);

        $countAccount = SocialConfig::where('token', $verifyToken)->count();

        if ($countAccount == 1) {
            \Log::info("Insagram Webhook (Verify Webhook) => Webhook Verified", ['token' => $verifyToken]);
            echo $challange;
        } else {
            \Log::info("Insagram Webhook (Verify Webhook) => Webhook not Verified", ['token' => $verifyToken]);
        }
    }

    public function receiveMessage(Request $request)
    {
        $data = $request->all();
        foreach ($data['entry'] as $entry) {
            foreach ($entry['messaging'] as $message) {

                if (!isset($message['message']['text'])) continue;

                $senderId = $message['sender']['id'];
                $recipientId = $message['recipient']['id'];
                $type = SocialContactThread::RECEIVE;
                $senderAccount = SocialConfig::where('account_id', $recipientId)->first();

                
                if (!$senderAccount) {
                    $temp = $senderId;
                    $senderId = $recipientId;
                    $recipientId = $temp;
                    $type = SocialContactThread::SEND;
                }

                $messageId = $message['message']['mid'];
                $text = $message['message']['text'];
                $socialAccountId = $entry['id'];
                $sendindAt = \Carbon\Carbon::createFromTimestampMs($message['timestamp'])->toDateTimeString();
                $account = SocialConfig::where('account_id', $recipientId)->first();

                if ($account) {
                    $object = null;
                    if ($data['object'] == SocialContact::TEXT_INSTA) {
                        $object = SocialContact::INSTAGRAM;
                    } else if ($data['object'] == SocialContact::TEXT_FB) {
                        $object = SocialContact::FACEBOOK;
                    }
                    if ($object) {
                        $user = SocialContact::where('account_id', $senderId)->where('platform', $object)->first();
                        if (!$user) {
                            $curl = curl_init();

                            $url = sprintf('https://graph.facebook.com/v12.0/%s?fields=%s&access_token=%s', $senderId, 'id,name', $account->page_token);

                            curl_setopt_array($curl, array(
                                CURLOPT_URL => $url,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'GET',
                            ));

                            $response = json_decode(curl_exec($curl), true);

                            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            if ($httpcode == 200) {
                                $user = SocialContact::create([
                                    'account_id' => $senderId,
                                    'social_config_id' => $account->id,
                                    'name' => $response['name'],
                                    'platform' => $object
                                ]);

                                \Log::info("Webhook (Receive Message) => New user create", ['id' => $senderId, 'data' => $data, 'object' => $data['object']]);
                            }
                            curl_close($curl);
                        }

                        if ($user) {
                            SocialContactThread::create([
                                'social_contact_id' => $user->id,
                                'message_id' => $messageId,
                                'sender_id' => $message['sender']['id'],
                                'recipient_id' => $message['recipient']['id'],
                                'text' => $text,
                                'type' => $type,
                                'sending_at' => $sendindAt
                            ]);

                            \Log::info("Webhook (Receive Message) => Message Received", ['mid' => $messageId, 'data' => $data, 'object' => $data['object']]);
                        } else {
                            \Log::info("Webhook (Receive Message) => Object Type not found", ['object' => $data['object'], 'data' => $data, 'object' => $data['object']]);
                        }
                    } else {
                        \Log::info("Webhook (Receive Message) => User not found", ['id' => $senderId, 'data' => $data, 'object' => $data['object']]);
                    }
                } else {
                    \Log::info("Webhook (Receive Message) => Account not found ", ['id' => $socialAccountId, 'data' => $data, 'object' => $data['object']]);
                }
            }
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            $contact = SocialContact::with('socialConfig')->findOrFail($request->contactId);
            $input = $request->input;
            $data['recipient']['id'] = $contact->account_id;
            $data['message']['text'] = $input;
            $pageToken = $contact->socialConfig->page_token;
            $url = "https://graph.facebook.com/v12.0/me/messages?access_token={$pageToken}";

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            \Log::info("Send message request", ['data' => json_encode($data)]);

            \Log::info("Send message response", ['response' => $response]);

            if ($httpcode == 200) {
                return response()->json([
                    'message' => "Message sent successfully",
                ]);
            } else {
                $response = json_decode($response, true);
                return response()->json([
                    'message' => $response['error']['message'],
                ], $httpcode);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
