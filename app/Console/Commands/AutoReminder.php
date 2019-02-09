<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Customer;
use App\ChatMessage;
use Carbon\Carbon;

class AutoReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:auto-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending auto reminders to customers who didn\'t reply';

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
      $time_to_send = false;
      $params = [
        'number'  => NULL,
        'status'  => 1,
        'user_id' => 6
      ];

      $customers = Customer::with(['Orders' => function ($query) {
        $query->where('order_status', 'Proceed without Advance')->where('auto_messaged', 1)->latest();
      }])->whereHas('Orders', function ($query) {
        $query->where('order_status', 'Proceed without Advance')->where('auto_messaged', 1)->latest();
      })->get()->toArray();

      foreach ($customers as $customer) {
        foreach ($customer['orders'] as $order) {
          $time_diff = Carbon::parse($order['auto_messaged_date'])->diffInHours(Carbon::now());
          
          if ($time_diff == 24) {
            $params['customer_id'] = $customer['id'];
            $params['message'] = 'Reminder about COD after 24 hours';
            $time_to_send = true;
          }

          if ($time_diff == 72) {
            $params['customer_id'] = $customer['id'];
            $params['message'] = 'Reminder about COD after 72 hours';
            $time_to_send = true;
          }

          if ($time_to_send) {
            $chat_message = ChatMessage::where('customer_id', $customer['id'])->latest()->first();

            if (empty($chat_message->number)) {
              $chat_message = ChatMessage::create($params);
            }
          }
        }
      }
    }
}
