<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class ChatMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get-size-from-chat-messages';

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
       $chatMessage = \Illuminate\Support\Facades\DB::table('chat_messages')
                      ->leftJoin('customers', 'customers.id', '=', 'customer_id')
                      ->select('chat_messages.id', 'customers.id as customer_id', 'message', 'shoe_size', 'clothing_size')
                      ->get();
      if ($chatMessage) {
        foreach ($chatMessage as $message) {

        }
      }
    }
}
