<?php

namespace App\Http\Controllers;

use App\Social\SocialConfig;
use App\SocialContact;
use App\SocialContactThread;
use App\SocialWebhookLog;
use Illuminate\Http\Request;

class SocialWebhookController extends Controller
{
    /**
     * Verify Facebook and Instagram Webhook URL
     * 
     * @method GET
     * 
     * @param Request $request
     */
    public function verifyWebhook(Request $request)
    {
        $hub = $request->all();
        $verifyToken = $hub['hub_verify_token'];
        $challange = $hub['hub_challenge'];

        SocialWebhookLog::log(SocialWebhookLog::INFO, "Verify Webhook => Webhook Verifying.....", ['token' => $verifyToken, 'challange' => $challange]);

        $countAccount = SocialConfig::where('webhook_token', $verifyToken)->count();

        if ($countAccount == 1) {
            SocialWebhookLog::log(SocialWebhookLog::SUCCESS, "Verify Webhook => Webhook Verified", ['token' => $verifyToken, 'challange' => $challange]);
            echo $challange;
        } else {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, "Verify Webhook => Webhook not Verified", ['token' => $verifyToken, 'challange' => $challange]);
        }
    }

    /**
     * When Subscirbe Event fire on Facebook and Instagram
     * 
     * @method POST
     * 
     * @param Request $request
     */
    public function webhook(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        SocialWebhookLog::log(SocialWebhookLog::INFO, "Webhook => Request Body", ['data' => $data]);
        foreach ($data['entry'] as $entry) {
            if (isset($entry['messaging'])) {
                $this->receiveMessage($entry, $data);
            } else {
                SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook => Request Body entry type not found", ['data' => $data]);
            }
        }
    }

    /**
     * Enter type = messaging
     */
    private function receiveMessage($entry, $data)
    {
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

                        SocialWebhookLog::log(SocialWebhookLog::SUCCESS, "Webhook (Receive Message) => Message Received", ['mid' => $messageId, 'object' => $data['object'], 'data' => $data]);
                    } else {
                        SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (Receive Message) => User not found", ['id' => $senderId, 'object' => $data['object'], 'data' => $data]);
                    }
                } else {
                    SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (Receive Message) => Object Type not found", ['object' => $data['object'], 'data' => $data, 'object' => $data['object'], 'data' => $data]);
                }
            } else {
                SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (Receive Message) => Account not found", ['id' => $socialAccountId, 'object' => $data['object'], 'data' => $data]);
            }
        }
    }
}
