<?php

namespace App\Console\Commands;

use App\Account;
use App\ColdLeadBroadcasts;
use App\Services\Instagram\Broadcast;
use Illuminate\Console\Command;

class SendBroadcastMessageToColdLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cold-leads:send-broadcast-messages';

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
//        $broadcasts = ColdLeadBroadcasts::where('started_at', 'LIKE', '%'.date('Y-m-d h-m').'%')
//                        ->where('frequency_completed', 0)
//                        ->get();

        $broadcasts = ColdLeadBroadcasts::where('frequency_completed', 0)
            ->where('status', 1)
            ->get();

        $bs = new Broadcast();
        $account = Account::where('last_name', 'LIKE', '%james%')->first();


        foreach ($broadcasts as $broadcast) {
            $leads = $broadcast->lead()->get();
            $message = $broadcast->message;
            $bs->login($account);
            $successCount = $bs->sendBulkMessages($leads, $message, $broadcast->image, $account);
            $broadcast->status = 0;
            $broadcast->frequency_completed = 1;
            $broadcast->messages_sent = $successCount;
            $broadcast->save();
        }

    }
}
