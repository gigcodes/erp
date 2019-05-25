<?php

namespace App\Services\Instagram;


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

            }
            $receipts[$lead->id] = $lead->platform_id;
        }


        foreach ($receipts as $key=>$receipt) {
            $this->instagram->direct->sendText([
                'users' => [$receipt]
            ], $message);
            sleep(1);

            //Save messages locally as well...

            $cl = ColdLeads::where('platform_id', $receipt)->first();
            $ct = InstagramThread::where('cold_lead_id', $cl->id)->first();

            if (!$ct) {
                $t = new InstagramThread();
                $t->col_lead_id = $cl->id;
                $t->account_id = $account->id;
                $t->save();
            }

            $m = new InstagramDirectMessages();
            $m->instagram_thread_id = $ct->id;
            $m->message = $message;
            $m->sender_id = $this->instagram->account_id;
            $m->receiver_id = $receipt;
            $m->message_type = 1;
            $m->save();


            if ($file !== null) {
                $fileName = public_path().'/uploads/'.$file;
                $fileToSend = new InstagramPhoto($fileName);
                $this->instagram->direct->sendPhoto([
                    'users' => [$receipt]
                ], $fileToSend->getFile());

                $m = new InstagramDirectMessages();
                $m->instagram_thread_id = $ct->id;
                $m->message = $file;
                $m->sender_id = $this->instagram->account_id;
                $m->message_type = 2;
                $m->receiver_id = $receipt;
                $m->save();
            }
            sleep(2);

            $l = ColdLeads::where('id', $key)->first();
            ++$l->messages_sent;
            $l->save();
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
}
