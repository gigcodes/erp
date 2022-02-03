<?php

namespace App\Http\Controllers;

use App\Social\SocialConfig;
use App\SocialContact;
use App\SocialContactThread;
use App\SocialWebhookLog;
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

        // \Log::channel('social_webhook')->info("Insagram Webhook (Verify Webhook) => Webhook Verifying.....",['token' => $verifyToken, 'challange' => $challange] );
        
        SocialWebhookLog::log(SocialWebhookLog::INFO, "Verify Webhook => Webhook Verifying.....", ['token' => $verifyToken, 'challange' => $challange]);
        
        $countAccount = SocialConfig::where('webhook_token', $verifyToken)->count();

        if ($countAccount == 1) {
            // \Log::channel('social_webhook')->info("Insagram Webhook (Verify Webhook) => Webhook Verified", ['token' => $verifyToken, 'challange' => $challange]);
            SocialWebhookLog::log(SocialWebhookLog::SUCCESS, "Verify Webhook => Webhook Verified", ['token' => $verifyToken, 'challange' => $challange]);
            echo $challange;
        } else {
            // \Log::channel('social_webhook')->info("Insagram Webhook (Verify Webhook) => Webhook not Verified", ['token' => $verifyToken, 'challange' => $challange]);
            SocialWebhookLog::log(SocialWebhookLog::ERROR, "Verify Webhook => Webhook not Verified", ['token' => $verifyToken, 'challange' => $challange]);
        }
    }

    public function receiveMessage(Request $request)
    {
        $data = json_decode($request->getContent(), true);
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

                            SocialWebhookLog::log(SocialWebhookLog::INFO, "Webhook (Receive Message) => Fetched user details using Page access Token", ['response' => $response, 'object' => $data['object'], 'data' => $data]);

                            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            if ($httpcode == 200) {
                                $user = SocialContact::create([
                                    'account_id' => $senderId,
                                    'social_config_id' => $account->id,
                                    'name' => $response['name'],
                                    'platform' => $object
                                ]);

                                // \Log::channel('social_webhook')->info("Webhook (Receive Message) => New user create", ['id' => $senderId, 'data' => $data, 'object' => $data['object']]);
                                SocialWebhookLog::log(SocialWebhookLog::INFO, "Webhook (Receive Message) => New user create", ['id' => $senderId, 'object' => $data['object'], 'data' => $data]);
                                
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

                            // \Log::channel('social_webhook')->info("Webhook (Receive Message) => Message Received", ['mid' => $messageId, 'object' => $data['object']]);
                            SocialWebhookLog::log(SocialWebhookLog::SUCCESS, "Webhook (Receive Message) => Message Received", ['mid' => $messageId, 'object' => $data['object'], 'data' => $data]);
                        } else {
                            // \Log::channel('social_webhook')->info("Webhook (Receive Message) => User not found", ['id' => $senderId, 'object' => $data['object'], 'data' => $data]);
                            SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (Receive Message) => User not found", ['id' => $senderId, 'object' => $data['object'], 'data' => $data]);
                        }
                    } else {
                        // \Log::channel('social_webhook')->info("Webhook (Receive Message) => Object Type not found", ['object' => $data['object'], 'data' => $data, 'object' => $data['object'], 'data' => $data]);
                        SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (Receive Message) => Object Type not found", ['object' => $data['object'], 'data' => $data, 'object' => $data['object'], 'data' => $data]);
                    }
                } else {
                    // \Log::channel('social_webhook')->info("Webhook (Receive Message) => Account not found ", ['id' => $socialAccountId, 'object' => $data['object'], 'data' => $data]);
                    SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (Receive Message) => Account not found", ['id' => $socialAccountId, 'object' => $data['object'], 'data' => $data]);
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

            // \Log::channel('social_webhook')->info("Send message request", ['data' => json_encode($data)]);
            SocialWebhookLog::log(SocialWebhookLog::INFO, "Send message request", ['data' => $data]);

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


            // \Log::channel('social_webhook')->info("Send message response", ['response' => $response]);
            SocialWebhookLog::log(SocialWebhookLog::INFO, "Send message response", ['response' => $response, 'data' => $data]);

            if ($httpcode == 200) {
                SocialWebhookLog::log(SocialWebhookLog::INFO, "Message sent successfully", ['response' => $response, 'data' => $data]);
                return response()->json([
                    'message' => "Message sent successfully",
                ]);
            } else {
                $response = json_decode($response, true);
                SocialWebhookLog::log(SocialWebhookLog::INFO, "Message not send", ['error' => $response['error']['message'], 'data' => $data]);
                return response()->json([
                    'message' => $response['error']['message'],
                ], $httpcode);
            }
        } catch (\Exception $e) {
            SocialWebhookLog::log(SocialWebhookLog::INFO, "Message not send", ['error' => $e->getMessage(), 'data' => $data]);
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
