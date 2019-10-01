<?php

namespace App\Console\Commands\Manual;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ChatMessage;
use App\Customer;
use App\Http\Controllers\WhatsAppController;

class WhatsappMoveToNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:move-to-new';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move all WhatsApp clients to a new number';

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
        // Set number to change
        $number = '91915273148%';
        $newNumber = '971562744570';
        $days = 1;
        $message = "Greetings from Solo Luxury  , we have moved our customer service to Dubai and you will receive all further messages from our Dubai number , in case you have sent any messages to us in the last 6  hours please resend it  , so that we can respond to it as some messages may have been missed out .";

        // Query to find all customers of $number
        $sql = "
            SELECT
                DISTINCT(customer_id)
            FROM
                chat_messages
            WHERE
                customer_id IN (
                    SELECT
                        c.id
                    FROM
                        customers c
                    WHERE
                        c.whatsapp_number LIKE '" . $number . "' AND 
                        do_not_disturb=0 AND
                        is_blocked=0                
                ) AND 
                number IS NOT NULL AND 
                created_at > DATE_SUB(NOW(), INTERVAL " . $days . " DAY)
        ";
        $rs = DB::select(DB::raw($sql));

        // Loop over customers
        if ($rs !== null) {
            foreach ($rs as $result) {
                // Find customer
                $customer = Customer::find($result->customer_id);
                $customer->whatsapp_number = $newNumber;
                $customer->save();

                // Send messages
                $params = [
                    'number' => null,
                    'user_id' => 6,
                    'approved' => 1,
                    'status' => 1,
                    'customer_id' => $result->customer_id,
                    'message' => $message
                ];
                $chat_message = ChatMessage::create($params);

                // Approve message
                $myRequest = new Request();
                $myRequest->setMethod('POST');
                $myRequest->request->add(['messageId' => $chat_message->id]);
                app(WhatsAppController::class)->approveMessage('customer', $myRequest);
            }
        }
    }
}
