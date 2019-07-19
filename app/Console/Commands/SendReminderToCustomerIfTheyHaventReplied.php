<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\Customer;
use App\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendReminderToCustomerIfTheyHaventReplied extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:send-to-customers';

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
        $now = Carbon::now()->toDateTimeString();

        $messagesIds = DB::table('chat_messages')
            ->selectRaw('MAX(id) as id, customer_id')
            ->groupBy('customer_id')
            ->whereNotNull('message')
            ->where('customer_id', '>', '0')
            ->where(function($query) {
                $query->whereIn('status', [7,8,9])->orWhere('approved', 1);
            })
            ->get();


        foreach ($messagesIds as $messagesId) {
            $customer = Customer::find($messagesId->customer_id);
            if (!$customer) {
                continue;
            }
            dump('customer found...');
            $frequency = $customer->frequency;
            if (!($frequency > 5)) {
                continue;
            }
            $message = ChatMessage::whereRaw('TIMESTAMPDIFF(MINUTE, `created_at`, "'.$now.'") >= ' . $frequency)
                ->where('id', $messagesId->id)
                ->where('user_id', '>', '0')
//                ->where('customer_id', $customer->id)
                ->first();

            if (!$message) {
                dump('no message...');
                continue;
            }

            dump('saving...');

            $templateMessage = $customer->reminder_message . ' - REMINDER123456789';

            $data['customer_id'] = $customer->id;
            $data['message'] = $templateMessage;
            $data['approved'] = 0;
            $data['user_id'] = 6;
            $data['status'] = 1;
            ChatMessage::create($data);
        }

    }
}
