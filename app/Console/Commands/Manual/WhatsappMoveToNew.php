<?php

namespace App\Console\Commands\Manual;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        $number = '919152731483';
        $days = 30;
        $message = "Greetings from Solo Luxury. We have moved our customer service to Dubai and you will receive all further messages from our Dubai number. In case you have sent any messages to us in the last 2 hours please resend it  - so that we can respond to it as some messages may have been missed out.";

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
                customer_id = 44 AND
                created_at > DATE_SUB(NOW(), INTERVAL " . $days . " DAY)
        ";
        $rs = DB::select( DB::raw( $sql ) );

        // Loop over customers
        if ( $rs !== NULL ) {
            foreach ( $rs as $customer ) {
                // Check if we have received a message in the last $days days
                $request = new Request();
                $request->setMethod('POST');
                $request->request->add([
                    'customer_id' => $customer->id,
                    'message' => $message,
                    'status' => 1
                ]);

                app(WhatsAppController::class)->sendMessage($request, 'customer');
            }
        }
    }
}
