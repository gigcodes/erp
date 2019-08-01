<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\Supplier;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendReminderToVendorIfTheyHaventReplied extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:send-to-vendor';

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
            ->selectRaw('MAX(id) as id, vendor_id')
            ->groupBy('vendor_id')
            ->whereNotNull('message')
            ->where('vendor_id', '>', '0')
            ->where(function($query) {
                $query->whereNotIn('status', [7,8,9]);
            })
            ->get();



        foreach ($messagesIds as $messagesId) {
            $vendor = Vendor::find($messagesId->vendor_id);
            if (!$vendor) {
                continue;
            }

            $frequency = $vendor->frequency;
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

            $templateMessage = $vendor->reminder_message;

            $data['vendor_id'] = $vendor->id;
            $data['message'] = $templateMessage;
            $data['approved'] = 0;
            $data['user_id'] = 6;
            $data['status'] = 1;
            ChatMessage::create($data);
        }

    }
}
