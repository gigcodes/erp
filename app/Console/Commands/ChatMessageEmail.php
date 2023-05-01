<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ChatMessageEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:copy-from-chat-message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email copy from chat messages';

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
        //
        $sql = "SELECT DISTINCT REGEXP_SUBSTR(`message`, '([a-zA-Z0-9._%+\-]+)@([a-zA-Z0-9.-]+)\.([a-zA-Z]{2,4})') AS Email,customer_id FROM `chat_messages` where customer_id >0  having Email is not null and Email != ''";
        $records = \DB::select($sql);
        $no = 0;
        if (! empty($records)) {
            foreach ($records as $r) {
                $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
                preg_match_all($pattern, $r->Email, $matches);
                if (isset($matches[0][0]) && ! empty($matches[0][0])) {
                    $customer = \App\Customer::where('id', $r->customer_id)->first();
                    if ($customer) {
                        if (empty($customer->email)) {
                            $customer->email = strtolower($matches[0][0]);
                            $customer->save();
                            $no++;
                        }
                    }
                }
            }
        }

        echo $no . ' Customer has been updated with email from chat message';
    }
}
