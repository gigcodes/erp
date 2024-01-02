<?php

namespace App\Console\Commands;

use App\Voip\Twilio;
use App\Helpers\TwilioHelper;
use Illuminate\Console\Command;

class TwilioErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twilio:errors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get twilio errors';

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
     * Execute the console command for twilio call logs to save in CallBusyMessage.
     *
     * @uses Twilio Model class
     *
     * @return mixed
     */
    public function handle()
    {
        $geterrors = \App\TwilioCredential::all();
        if ($geterrors) {
            foreach ($geterrors as $_error) {
                $call_history = \App\TwilioCallData::where(['account_sid' => $_error->account_id])->get();
                if ($call_history) {
                    foreach ($call_history as $_history) {
                        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $_error->account_id . '/Calls/' . $_history->call_sid . '/Notifications.json';
                        $result = TwilioHelper::curlGetRequest($url, $_error->account_id, $_error->auth_token);
                        $result = json_decode($result);
                        if ($result) {
                            if (isset($result->notifications) && count($result->notifications)) {
                                foreach ($result->notifications as $notification) {
                                    $input['sid'] = $notification->sid;
                                    $input['account_sid'] = $notification->account_sid;
                                    $input['call_sid'] = $notification->call_sid;
                                    $input['error_code'] = $notification->error_code;
                                    $input['message_text'] = $notification->message_text;
                                    $input['message_date'] = $notification->message_date;
                                    $error = \App\TwilioError::create($input);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
