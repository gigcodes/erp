<?php

namespace App\Console\Commands;

use App\ChatMessage;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\Whatsapp\ChatApi\ChatApi;

class SendQueuePendingChatMessages extends Command
{
    const BROADCAST_PRIORITY        = 8;
    const MARKETING_MESSAGE_TYPE_ID = 3;

    public $waitingMessages;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:queue-pending-chat-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send queue pending chat messages, run at every 3rd minute';

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
        try {


            $report = \App\CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            // get the status for approval
            $approveMessage = \App\Helpers\DevelopmentHelper::needToApproveMessage();
            // get the status for approval
            $approveMessage = \App\Helpers\DevelopmentHelper::needToApproveMessage();
            $limit = ChatMessage::getQueueLimit();

            // if message is approve then only need to run the queue
            if ($approveMessage == 1) {
                
                $allWhatsappNo         = config("apiwha.instances");
                
                $this->waitingMessages = [];
                if (!empty($allWhatsappNo)) {
                    foreach ($allWhatsappNo as $no => $dataInstance) {
                        $no = ($no == 0) ? $dataInstance["number"] : $no;
                        $chatApi = new ChatApi;
                        $waitingRprt = $chatApi->chatQueue($no);
                        $waitingMessage = 0;
                        if(!empty($waitingRprt["totalMessages"])) {
                            $waitingMessage = $waitingRprt["totalMessages"];
                        }
                        $this->waitingMessages[$no] = $waitingMessage;
                    }
                }

                echo '<pre>'; print_r($this->waitingMessages); echo '</pre>';exit;

                $chatMessage = ChatMessage::where('is_queue', ">", 0)->limit($limit)->get();


                foreach ($chatMessage as $value) {
                    // check first if message need to be send from broadcast
                    if ($value->is_queue > 1) {
                        $sendNumber = \DB::table("whatsapp_configs")->where("id", $value->is_queue)->first();
                        // if chat message has image then send as a multiple message
                        if ($images = $value->getMedia(config('constants.media_tags'))) {
                            foreach ($images as $k => $image) {
                                \App\ImQueue::create([
                                    "im_client"                 => "whatsapp",
                                    "number_to"                 => $value->customer->phone,
                                    "number_from"               => ($sendNumber) ? $sendNumber->number : $value->customer->whatsapp_number,
                                    "text"                      => ($k == 0) ? $value->message : "",
                                    "image"                     => $image->getUrl(),
                                    "priority"                  => self::BROADCAST_PRIORITY,
                                    "marketing_message_type_id" => self::MARKETING_MESSAGE_TYPE_ID,
                                ]);
                            }
                        } else {
                            \App\ImQueue::create([
                                "im_client"                 => "whatsapp",
                                "number_to"                 => $value->customer->phone,
                                "number_from"               => ($sendNumber) ? $sendNumber->number : $value->customer->whatsapp_number,
                                "text"                      => $value->message,
                                "priority"                  => self::BROADCAST_PRIORITY,
                                "marketing_message_type_id" => self::MARKETING_MESSAGE_TYPE_ID,
                            ]);
                        }

                        $value->is_queue = 0;
                        $value->save();

                    } else {
                        $myRequest = new Request();
                        $myRequest->setMethod('POST');
                        $myRequest->request->add(['messageId' => $value->id]);
                        app('App\Http\Controllers\WhatsAppController')->approveMessage('customer', $myRequest);
                    }

                }
            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            echo '<pre>'; print_r($e->getMessage()); echo '</pre>';exit;
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }

    }
}
