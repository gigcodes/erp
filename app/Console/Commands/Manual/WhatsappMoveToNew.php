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

        // Settings
        $newNumber = [
            '971547763482', // 04
            '971545889192',
            '971562744570' // 06
        ];
        $message = "Greetings from Solo Luxury , our offices have moved to Dubai , and this is our new whats app number , Best Wishes - Solo Luxury "; // MESSAGE FOR ACTIVE CUSTOMERS OVER 60 DAYS
        $maxPerNumber = 10;

        // Query to find all customers of $number
        $sql = "
            SELECT
                id
            FROM
                customers
            WHERE
                whatsapp_number LIKE '" . $number . "' AND
                phone IS NOT NULL AND
                phone != '' AND
                do_not_disturb=0 AND
                is_blocked=0
            ORDER BY
                RAND()
            LIMIT
                0," . (count($newNumber) * $maxPerNumber) . "
        ";
        // echo $sql;
        $rs = DB::select(DB::raw($sql));

        // Set current number
        $currentNewNumber = $newNumber[ 0 ];

        // Set count to 0
        $count = 0;
        $arrCount = 0;

        // Loop over customers
        if ($rs !== null) {
            foreach ($rs as $result) {
                $customer = Customer::find($result->id);
                $customer->whatsapp_number = $currentNewNumber;
                $customer->save();

                // Output customer information
                echo $customer->id . ' ' . $customer->phone . "\n";

                // Send messages
                $params = [
                    'number' => null,
                    'user_id' => 6,
                    'approved' => 1,
                    'status' => 8,
                    'customer_id' => $result->id,
                    // 'customer_id' => 44, // FOR TESTING
                    'message' => $message
                ];
                $chat_message = ChatMessage::create($params);

                // Approve message
                $myRequest = new Request();
                $myRequest->setMethod('POST');
                $myRequest->request->add(['messageId' => $chat_message->id]);
                echo " ... SENDING from " . $currentNewNumber . "\n";
                app(WhatsAppController::class)->approveMessage('customer', $myRequest);

                // Check if we have reached the max
                $count++;
                if ($count % $maxPerNumber == 0) {
                    // Update array counter
                    $arrCount++;

                    // Count is numbers times max per number?
                    if ($count == ($maxPerNumber * count($newNumber))) {
                        exit("DONE");
                    }

                    // Set current new number
                    $currentNewNumber = $newNumber[ $arrCount ];
                }
            }
        }
    }
}
