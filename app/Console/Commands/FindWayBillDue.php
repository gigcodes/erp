<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\CronJobReport;
use App\Waybillinvoice;
use Illuminate\Console\Command;

/**
 * @author Sukhwinder <sukhwinder@sifars.com>
 * This command takes care of receiving all the emails from the smtp set in the environment
 *
 * All fetched emails will go inside emails table
 */
class FindWayBillDue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find:waybilldue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check date - if date over then status will be due';

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
        $report = CronJobReport::create([
            'signature' => $this->signature,
            'start_time' => Carbon::now(),
        ]);

        Waybillinvoice::where('status', '!=', 'paid')->whereDate('due_date', '<', Carbon::today())->update(['status' => 'due']);
    }
}
