<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Twilio\Rest\Client;
use App\ChatMessage;
use App\Customer;
use Carbon\Carbon;

class SendQueuedMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:send_message'; 

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
		//$now = Carbon::now()->format('Y-m-d H:i:s');
         $chatMessage = ChatMessage::where('is_queue', 1)
                            ->join("customers as c", "c.id", "chat_messages.customer_id")
                            ->where("chat_messages.message_application_id", 3)
                           ->where(function ($q) {
								$q->whereNull("chat_messages.scheduled_at")->orWhere("chat_messages.scheduled_at", '<=', Carbon::now()->format('Y-m-d H:i:s'));
							})
                            ->select("chat_messages.*", 'c.store_website_id')
                            ->get();
							
			if (!$chatMessage->isEmpty()) {
                foreach ($chatMessage as $value) {
					$twilio_cred = \App\StoreWebsiteTwilioNumber::select('twilio_active_numbers.account_sid as a_sid', 'twilio_active_numbers.phone_number as phone_number', 'twilio_credentials.auth_token as auth_token')
						->join('twilio_active_numbers', 'twilio_active_numbers.id', '=', 'store_website_twilio_numbers.twilio_active_number_id')
						->join('twilio_credentials', 'twilio_credentials.id', '=', 'twilio_active_numbers.twilio_credential_id')
						->where('store_website_twilio_numbers.store_website_id', $value->store_website_id)
						->first();

					if(isset($twilio_cred)){
						$account_sid = $twilio_cred->a_sid;
						$auth_token = $twilio_cred->auth_token;
						$twilio_number = $twilio_cred->phone_number;
					}else{
						$account_sid = "AC23d37fbaf2f8a851f850aa526464ee7d";
						$auth_token = "51e2bf471c33a48332ea365ae47a6517";
						$twilio_number = "+18318880662";
					}
					try{
						$receiverNumber = '+'.$value['phone'];
						$message = $value['message'];
						$client = new Client($account_sid, $auth_token);
						$client->messages->create($receiverNumber, [
							'from' => $twilio_number,
							'body' => $message
						]);
						ChatMessage::where('id', $value['id'])->update([
							'send_by'=>$twilio_number,
							"user_id" => \Auth::id(),
							'approved'=>1,
							'is_delivered'=>1,
							'is_queue'=>0,
						]);
					}catch (Exception $e) {
						\Log::info("Sending SMS issue #2215 ->" . $e->getMessage());
					}
				}
			}
    }
}
