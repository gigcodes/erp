<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Cache;

class LoadLastCronErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:last-errors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load last cron errors';

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
        try {
            $report = \App\CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            Cache::remember('cronLastErrors',15,function() {
                return \App\CronJob::join("cron_job_reports as cjr","cron_jobs.signature","cjr.signature")
                ->where("cjr.start_time",'>', \DB::raw('NOW() - INTERVAL 24 HOUR'))->where("cron_jobs.last_status","error")->groupBy("cron_jobs.signature")->get();
            });
            
            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
