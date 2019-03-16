<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MessageQueue;
use App\Customer;
use Carbon\Carbon;
use App\Jobs\SendMessageToAll;
use App\Jobs\SendMessageToSelected;

class RunMessageQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:message-queues';

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
      $message_queues = MessageQueue::where('sending_time', '<=', Carbon::now())->where('sent', 0)->where('status', '!=', 1)->limit(10);

      if (count($message_queues->get()) > 0) {
        foreach ($message_queues->get() as $message) {
          if ($message->type == 'message_all') {
            $customer = Customer::find($message->customer_id);

            if ($customer->do_not_disturb == 0)
              SendMessageToAll::dispatch($message->user_id, $customer, json_decode($message->data, true), $message->id);
          } else {
            SendMessageToSelected::dispatch($message->phone, json_decode($message->data, true), $message->id);
          }
        }

        // $message_queues->delete();
      }
    }
}
