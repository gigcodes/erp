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
            [
                'number' => '971547763482', // 04
                'count' => 10
            ],
            [
                'number' => '971545889192',
                'count' => 10
            ],
            [
                'number' => '971562744570', // 06
                'count' => 10
            ],
            [
                'number' => '971504289967',
                'count' => 1
            ],
        ];
        $message = "Greetings from Solo Luxury , our offices have moved to Dubai , and this is our new whats app number , Best Wishes - Solo Luxury "; // MESSAGE FOR ACTIVE CUSTOMERS OVER 60 DAYS

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
                is_blocked=0 AND
                deleted_at IS NULL
            ORDER BY
                RAND()
            LIMIT
                0,31
        ";
        // echo $sql;
        $rs = DB::select(DB::raw($sql));

        // Set current number
        $currentNewNumber = $newNumber[ 0 ][ 'number' ];

        // Set count to 0
        $count = 0;
        $arrCount = 0;

        // Loop over customers
        if ($rs !== null) {
            foreach ($rs as $result) {
                // Get customer from database
                $customer = Customer::find($result->id);

                // Update count
                $count++;

                // Update number if we have a customer
                if ($customer !== null) {
                    // Output customer information
                    echo $count . " " . $customer->id . ' ' . $customer->phone . " moved from " . $customer->whatsapp_number . " => " . $currentNewNumber . "\n";

                    // Update WhatsApp number
                    $customer->whatsapp_number = $currentNewNumber;
                    $customer->save();

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
//                    $myRequest = new Request();
//                    $myRequest->setMethod('POST');
//                    $myRequest->request->add(['messageId' => $chat_message->id]);
//                    echo " ... SENDING from " . $currentNewNumber . "\n";
//                    app(WhatsAppController::class)->approveMessage('customer', $myRequest);

                } else {
                    echo $count . " Customer ID " . $result->id . " ERROR\n";
                }

                // Check if we have reached the max
                if ($count == $newNumber[ $arrCount ][ 'count' ]) {
                    // Update array counter
                    $arrCount++;

                    // Reset count
                    $count = 0;

                    // Exit
                    if ( count($newNumber) <= $arrCount ) {
                        exit("DONE");
                    }

                    // Set current new number
                    $currentNewNumber = $newNumber[ $arrCount ][ 'number' ];
                }
            }
        }
    }
}
