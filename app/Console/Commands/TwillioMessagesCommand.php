<?php

namespace App\Console\Commands;

use App\MarketingMessage;
use App\MarketingMessageCustomer;
use App\MessagingGroup;
use App\SmsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Twilio\Rest\Client;

class TwillioMessagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:twillio_messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command For Twillio Messages';

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
        $date = Carbon::now();
        $date_added_hour = Carbon::now()->addHours(1);
        $services = SmsService::all();
        foreach ($services as $service) {
            if (strtolower($service['name']) == 'twilio') {
                $groups = MessagingGroup::where('service_id', $service['id'])->get();
                foreach ($groups as $group) {
                    $twilio_cred = \App\StoreWebsiteTwilioNumber::select('twilio_active_numbers.account_sid as a_sid', 'twilio_active_numbers.phone_number as phone_number', 'twilio_credentials.auth_token as auth_token')
                    ->join('twilio_active_numbers', 'twilio_active_numbers.id', '=', 'store_website_twilio_numbers.twilio_active_number_id')
                    ->join('twilio_credentials', 'twilio_credentials.id', '=', 'twilio_active_numbers.twilio_credential_id')
                    ->where('store_website_twilio_numbers.store_website_id', $group->store_website_id)
                    ->first();

                    if (isset($twilio_cred)) {
                        $account_sid = $twilio_cred->a_sid;
                        $auth_token = $twilio_cred->auth_token;
                        $twilio_number = $twilio_cred->phone_number;
                    } else {
                        $account_sid = 'AC23d37fbaf2f8a851f850aa526464ee7d';
                        $auth_token = '51e2bf471c33a48332ea365ae47a6517';
                        $twilio_number = '+18318880662';
                    }

                    $marketing_messages = MarketingMessage::where('message_group_id', $group->id)->where('is_sent', 0)->whereBetween('scheduled_at', [$date, $date_added_hour])->get();
                    //$marketing_messages = MarketingMessage::where('message_group_id', $group->id)->where('is_sent', 0)->get();
                    foreach ($marketing_messages as $index => $message) {
                        $marketingMessageCustomers = MarketingMessageCustomer::leftJoin('customers', 'customers.id', '=', 'marketing_message_customers.customer_id')
                        ->where('marketing_message_id', $message->id)->select('marketing_message_customers.*', 'customers.phone')->get();
                        foreach ($marketingMessageCustomers as $marketingMessageCustomer) {
                            try {
                                // Get APP_URL from env because in console Request is not available
                                $appUrl = config('env.CALL_BACK_URL');
                                $lastchar = $appUrl[-1];

                                if (strcmp($lastchar, '/') !== 0) {
                                    $appUrl = $appUrl.'/';
                                }

                                $client = new Client($account_sid, $auth_token);
                                $client->messages->create('+'.$marketingMessageCustomer['phone'], [
                                    'from' => $twilio_number,
                                    'body' => $message['title'],
                                    'statusCallback' => $appUrl.'twilio/handleMessageDeliveryStatus/'.$marketingMessageCustomer['customer_id'].'/'.$marketingMessageCustomer['id'],
                                ]);
                                MarketingMessageCustomer::where(['customer_id' => $marketingMessageCustomer['customer_id'], 'marketing_message_id' => $message->id])->update(['is_sent' => 1]);
                            } catch (Exception $e) {
                                \Log::info('Sending SMS issue #2215 ->'.$e->getMessage());
                            }
                        }
                        $message->update(['is_sent' => 1]);
                    }
                }
            }
        }
    }
}
