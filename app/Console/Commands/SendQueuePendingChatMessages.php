<?php

namespace App\Console\Commands;

use App\ChatMessage;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class SendQueuePendingChatMessages extends Command
{
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
        // get the status for approval
        $approveMessage = \App\Helpers\DevelopmentHelper::needToApproveMessage();

        // if message is approve then only need to run the queue
        if($approveMessage == 1) {
            $chatMessage = ChatMessage::where('is_queue',">", 0)->limit(10)->get();
            foreach ($chatMessage as $value) {

                if($value->is_queue > 1) {
                   $sendNumber = \DB::table("whatsapp_configs")->where("id",$value->is_queue)->first(); 
                   \App\ImQueue::create([
                        "im_client" => "whatsapp",
                        "number_to" => $chatMessage->customer->phone,
                        "number_from" => ($sendNumber) ? $sendNumber : $chatMessage->customer->whatsapp_number,
                        "text" => $chatMessage->message
                    ]); 
                
                }else{
                    $myRequest = new Request();
                    $myRequest->setMethod('POST');
                    $myRequest->request->add(['messageId' => $value->id]);
                    app('App\Http\Controllers\WhatsAppController')->approveMessage('customer', $myRequest);
                }

            }
        }

    }
}
