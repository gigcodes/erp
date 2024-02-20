<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Voip\Twilio;
use App\CronJobReport;
use Illuminate\Console\Command;

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
     *
     * @return mixed
     *
     * @uses Twilio Model class
     */
    public function handle()
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $twilio = new Twilio();
            $twilio->missedCallStatus();
            exit('This data inserted in db..Now, you can check missed calls screen');

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
