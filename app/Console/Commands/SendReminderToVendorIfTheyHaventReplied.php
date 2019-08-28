<?php

namespace App\Console\Commands;

use App\AutoReply;
use App\ChatMessage;
use App\Http\Controllers\WhatsAppController;
use App\Supplier;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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

            $this->sendMessage($vendor->id, $templateMessage);
        }

    }

    private function sendMessage($vendorId, $message) {

        $params = [
            'number' => null,
            'user_id' => 6,
            'approved' => 1,
            'status' => 1,
            'vendor_id' => $vendorId,
            'message' => $message
        ];

        $chat_message = ChatMessage::create($params);

        $myRequest = new Request();
        $myRequest->setMethod('POST');
        $myRequest->request->add(['messageId' => $chat_message->id]);

        app(WhatsAppController::class)->approveMessage('vendor', $myRequest);
    }
}
