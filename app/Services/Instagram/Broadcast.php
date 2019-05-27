<?php

namespace App\Services\Instagram;


use App\Account;
use App\ColdLeads;
use App\Customer;
use App\InstagramDirectMessages;
use App\InstagramThread;
use App\InstagramUsersList;
use App\TargetLocation;
use Carbon\Carbon;
use InstagramAPI\Instagram;
use InstagramAPI\Media\Photo\InstagramPhoto;
use InstagramAPI\Signatures;

class Broadcast {


    /**
     * InstagramAPI\Instagram $instagram
     */
    public $instagram;
    private $token;

    public function login($account) {
        $instagram = new Instagram();
        $instagram->login($account->last_name, $account->password);
        $this->token = Signatures::generateUUID();
        $this->instagram = $instagram;
    }


    public function sendBulkMessages($leads, $message, $file = null, $account) {
        $receipts = [];
        foreach ($leads as $lead) {
            $receiverId = $lead->platform_id;
            if (strlen($receiverId) < 5) {
                try {
                    $receiverId = $this->instagram->people->getUserIdForName($lead->username);
                } catch (\Exception $exception) {
                    $lead->delete();
                    continue;
                }
                $lead->platform_id = $receiverId;
                $lead->save();
                sleep(2);

            }
            $receipts[$lead->id] = $lead->platform_id;
        }


        foreach ($receipts as $key=>$receipt) {
            $cl = ColdLeads::where('platform_id', $receipt)->first();
            $account_id = $cl->account_id;
            if ($account_id > 0) {
                $accountToSend = Account::find($account_id);
            } else {
                $accountToSend = Account::where('platform', 'sitejabber')->where('broadcast', 1)->orderBy('broadcasted_messages', 'ASC')->first();
            }

            $cl->account_id = $accountToSend->id;
            $cl->save();

            $this->sendText($message, $receipt, $accountToSend, $account);

            sleep(4);

            $ct = InstagramThread::where('cold_lead_id', $cl->id)->first();
            if (!$ct) {
                $ct = new InstagramThread();
                $ct->cold_lead_id = $cl->id;
                $ct->account_id = $account->id;
                $ct->save();
            }


            $m = new InstagramDirectMessages();
            $m->instagram_thread_id = $ct->id;
            $m->message = $message;
            $m->sender_id = $this->instagram->account_id;
            $m->receiver_id = $receipt;
            $m->message_type = 1;
            $m->save();


            if ($file !== null) {
                $this->sendImage($file, $receipt, $accountToSend, $account);

                $m = new InstagramDirectMessages();
                $m->instagram_thread_id = $ct->id;
                $m->message = $file;
                $m->sender_id = $this->instagram->account_id;
                $m->message_type = 2;
                $m->receiver_id = $receipt;
                $m->save();
            }
            sleep(5);

            $l = ColdLeads::where('id', $key)->first();
            ++$l->messages_sent;
            $l->save();

            unset($receipts[$key]);
        }

        return count($receipts);
    }

    public function addColdLeadsToCustomersIfMessagIsReplied() {

        $currentInstagramAccountId = $this->instagram->account_id;


        $inbox = $this->instagram->direct->getInbox()->asArray();

        if (!isset($inbox['inbox']['threads'])) {
            return;
        }

        $messages = $inbox['inbox']['threads'];

        foreach ($messages as $message) {
            $currentUserAccountId = $message['items'][0]['user_id'] ?? null;

            if ($currentInstagramAccountId !== $currentUserAccountId) {
                $this->createCustomer($currentUserAccountId);
                continue;
            }

            $thread = $this->instagram->direct->getThread($message['thread_id'])->asArray();
            $chats = $thread['thread']['items'];
            foreach ($chats as $chat) {
                if ($chat['user_id'] !== $currentInstagramAccountId) {
                    $this->createCustomer($chat['user_id']);
                    continue;
                }
            }

        }
    }

    private function createCustomer($instagramId) {
        $thread = InstagramDirectMessages::where('sender_id', $instagramId)->orWhere('receiver_id', $instagramId)->first();

        if (!$thread) {
            return;
        }

        $ct = InstagramThread::where('id', $thread->id)->first();
        if (!$ct) {
            return;
        }

        $coldLeadId = $ct->cold_lead_id;
        $lead = ColdLeads::where('id', $coldLeadId)->first();
        if (!$lead) {
            return;
        }

        $customer = new Customer();
        $customer->instahandler = $lead->username;
        $customer->ig_username = $lead->platform_id;
        $customer->name = $lead->name;
        $customer->save();

    }


    public function sendText($message, $receipt, $accountToSend, $account) {
        if ($account->id === $accountToSend->id) {
            $this->instagram->direct->sendText([
                'users' => [$receipt]
            ], $message);

            return;
        }

        $instagram = new Instagram();
        $instagram->login($accountToSend->last_name, $accountToSend->password);
        $instagram->direct->sendText([
            'users' => [$receipt]
        ], $message);

    }

    public function sendImage($file, $receipt, $accountToSend, $account) {
        $fileName = public_path().'/uploads/'.$file;
        $fileToSend = new InstagramPhoto($fileName);
        if ($account->id === $accountToSend->id) {
            $this->instagram->direct->sendPhoto([
                'users' => [$receipt]
            ], $fileToSend->getFile());

            return;
        }

        $instagram = new Instagram();
        $instagram->login($accountToSend->last_name, $accountToSend->password);
        $this->instagram->direct->sendPhoto([
            'users' => [$receipt]
        ], $fileToSend->getFile());
    }
}
