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
            $chatMessage = ChatMessage::where('is_queue', 1)->limit(10)->get();
            foreach ($chatMessage as $value) {
                $myRequest = new Request();
                $myRequest->setMethod('POST');
                $myRequest->request->add(['messageId' => $value->id]);
                app('App\Http\Controllers\WhatsAppController')->approveMessage('customer', $myRequest);
            }
        }

    }
}
