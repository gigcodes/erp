<?php

namespace App\Console\Commands;

use App\Setting;
//use App\InstagramThread;
use App\ChatMessage;
use Illuminate\Console\Command;
//use InstagramAPI\Instagram;
//use App\InstagramDirectMessagesHistory;
use Illuminate\Support\Facades\Log;

//Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class SendInstagramMessageInQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-instagram-message:in-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//        $accountSettingInfo = Setting::select('val')->where('name', 'instagram_message_queue_rate_setting')->first();
//        $accountSettingInformation = null;
//        if ($accountSettingInfo) {
//            $accountSettingInformation = json_decode($accountSettingInfo->val, true);
//        }
//
//        $allMessages =   ChatMessage::with('getSenderUsername')->whereNotNull('instagram_user_id')->whereNotNull('account_id')->where('is_queue', 1)->get();
//
//        $newAllMessages = [];
//
//        foreach ($allMessages as $mes) {
//            // dump($accountSettingInformation);
//            if (isset($accountSettingInformation[$mes->getSenderUsername->id]) &&  ($accountSettingInformation[$mes->getSenderUsername->id]>0) ) {
//                $newAllMessages[] = $mes;
//                $accountSettingInformation[$mes->getSenderUsername->id] =  $accountSettingInformation[$mes->getSenderUsername->id] - 1;
//            };
//        }
//
//        foreach ($newAllMessages as $mes) {
        ////            $thread = InstagramThread::where('scrap_influencer_id', $mes->getRecieverUsername->id)->first();
//            $sender = $mes->getSenderUsername->last_name;
//
//            $password = $mes->getSenderUsername->password;
//            $proxy = $mes->getSenderUsername->proxy;
//            $receiver = $mes->getRecieverUsername->username;
//            $message = $mes->message;
//            $sender_id = $mes->getSenderUsername->id;
//                Log::channel('insta_message_queue_by_rate_limit')->info('sender message limit:' . $accountSettingInformation[$sender_id] . 'sender name:' . $sender);
        ////                $i = new Instagram();
//                try {
        ////                    $i->setProxy($proxy);
        ////                    $i->login($sender, $password);
//                } catch (\Exception $exception) {
//                    \Log::error($sender . '::' . $exception->getMessage());
//                    // return false;
//                }
//
//                try {
        ////                    $receiver = $i->people->getUserIdForName($receiver);
//                } catch (\Exception $e) {
//                    // return false;
//                }
//
//                try {
//                    $resp = $i->direct->sendText([
//                        'users' => [
//                            $receiver
//                        ]
//                    ], $message);
//
//                    $history = [
//                        'thread_id'   => $thread->thread_id,
//                        'title'       => 'Send text message',
//                        'description' => 'Message send successfully',
//                        'created_at'  => now(),
//                    ];
//
//
//                   $last_updated =  $mes->update([
//                        'is_queue'=>2
//                        ]) ;
//                        Log::channel('insta_message_queue_by_rate_limit')->info('message send');
//                    // $mes->save();
//
//
//                } catch (\Exception $exception) {
//                    $history = [
//                        'thread_id'   => $thread->thread_id,
//                        'title'       => 'Send text message',
//                        'created_at'  => now(),
//                        'description' => $exception->getMessage()
//                    ];
//                }
//                InstagramDirectMessagesHistory::insert($history);

//        }
    }
}
