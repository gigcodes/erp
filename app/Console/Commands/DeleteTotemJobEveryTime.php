<?php

namespace App\Console\Commands;

use App\Helpers\LogHelper;
use Illuminate\Console\Command;

class DeleteTotemJobEveryTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'totem-jobs:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Totem Jobs need to delete';

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
        try {
            $jobs = \App\Job::where('payload', 'like', '%Totem%');
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Job query finished .']);
            /*if(!empty($limit)) {
                $jobs = $jobs->limit($limit);
            }*/

            $jobs = $jobs->get();

            if (! $jobs->isEmpty()) {
                foreach ($jobs as $job) {
                    echo $job->id . " started to delete \n";
                    LogHelper::createCustomLogForCron($this->signature, ['message' => "corn was started to delete \n"]);
                    $job->delete();
                }
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch(\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
