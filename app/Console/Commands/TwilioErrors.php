<?php

namespace App\Console\Commands;

use App\Helpers\TwilioHelper;
use App\Voip\Twilio;
use Illuminate\Console\Command;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * @uses Twilio Model class
     * @return mixed
     */
    public function handle()
    {
        $geterrors = \App\TwilioCredential::all();
        if ($geterrors) {
            foreach ($geterrors as $_error) {
                try {
                    $call_history = \App\TwilioCallData::where(['account_sid' => $_error->account_id])->get();
                    if ($call_history) {
                        foreach ($call_history as $_history) {
                            $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $_error->account_id . '/Calls/' . $_history->call_sid . '/Notifications.json';
                            $result = TwilioHelper::curlGetRequest($url, $_error->account_id, $_error->auth_token);
                            $result = json_decode($result);
                            echo "<pre/>";
                            print_r($result);
                            die();
                        }
                    }
                    return new JsonResponse(['status' => 1, 'message' => 'Number forwarded to agent successfully.']);
                } catch (\Exception $e) {
                    return new JsonResponse(['status' => 0, 'message' => $e->getMessage()]);
                }
            }
        }
    }
}
