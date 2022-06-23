<?php

namespace App\Console\Commands;

use App\CronJobReport;
use Illuminate\Console\Command;

class NegativeCouponResponses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:NegativeCouponResponses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Negative Coupon Response';

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
            
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
