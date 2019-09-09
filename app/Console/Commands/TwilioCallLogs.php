<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Voip\Twilio;

class TwilioCallLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twilio:allcalls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To save Twilio call logs in CallBusyMessage.';

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
        $twilio = new Twilio();
        $twilio->missedCallStatus();
        exit('This data inserted in db..Now, you can check missed calls screen');
    }
}
