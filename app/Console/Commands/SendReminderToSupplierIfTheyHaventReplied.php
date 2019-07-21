<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\Customer;
use App\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendReminderToSupplierIfTheyHaventReplied extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:send-to-supplier';

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
            ->selectRaw('MAX(id) as id, supplier_id')
            ->groupBy('supplier_id')
            ->whereNotNull('message')
            ->where('supplier_id', '>', '0')
            ->where(function($query) {
                $query->whereNotIn('status', [7,8,9]);
            })
            ->get();



        foreach ($messagesIds as $messagesId) {
            $supplier = Customer::find($messagesId->supplier_id);
            if (!$supplier) {
                continue;
            }

            $frequency = $supplier->frequency;
            if (!($frequency >= 5)) {
                continue;
            }

            $message = ChatMessage::whereRaw('TIMESTAMPDIFF(MINUTE, `updated_at`, "'.$now.'") >= ' . $frequency)
                ->where('id', $messagesId->id)
                ->where('user_id', '>', '0')
                ->where('approved', '1')
                ->first();


            if (!$message) {
                continue;
            }

            dump('saving...');

            $templateMessage = $supplier->reminder_message;

            $data['supplier_id'] = $supplier->id;
            $data['message'] = $templateMessage;
            $data['approved'] = 0;
            $data['user_id'] = 6;
            $data['status'] = 1;
            ChatMessage::create($data);
        }

    }
}
