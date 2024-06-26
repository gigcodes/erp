<?php

namespace App\Console\Commands;

use App\Account;
use Carbon\Carbon;
use App\CronJobReport;
use Illuminate\Console\Command;

class CreateCustomerIfColdLeadReplied extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cold-lead:create-if-replied';

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
        try {
            $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $accounts = Account::where('platform', 'instagram')->where('broadcast', 1)->get();

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
