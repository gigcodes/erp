<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Helpers\LogHelper;
use Illuminate\Console\Command;

class SendFailedJobReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-report:failed-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send failed jobs report every one 5 min';

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
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        $reportSubject = $this->signature . '-' . date('Y-M-D');

        try {
            $beforeFiveMin = Carbon::now()->subMinutes(5)->toDateTimeString();
            $failedReports = \DB::table('failed_jobs')->where('failed_at', '>', $beforeFiveMin)->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'failed jobs query finished.']);
            if (! $failedReports->isEmpty()) {
                throw new \Exception('Error Processing jobs, Total Failed Jobs in last five min : ' . $failedReports->count(), 1);
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch(\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
