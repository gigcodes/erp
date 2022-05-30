<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\StoreViewsGTMetrix;
use App\Repositories\GtMatrixRepository;

class GtMetrixReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gt-metrix:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'New GTMetrix Report';

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
        //Checking for all available urls
        $gtMatrixURLs = app(StoreViewsGTMetrix::class)->where('status', 'queued')->get();
        foreach($gtMatrixURLs as $gtMatrixURL){
            if(!$gtMatrixURL->account_id){
                $gtMatrixURL->update(['status' => 'error','reason' => 'No gt-metrix account assoicated with this test']);
                continue;
            }
            //app(GtMatrixRepository::class)->generateLog($gtMatrixURL);
            //Getting the record from gt-metrix
            app(GooglePageSpeedRepository::class)->generateReport($gtMatrixURL,$gtMatrixURL->account);

        }

    }
}
