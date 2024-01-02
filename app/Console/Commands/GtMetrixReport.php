<?php

namespace App\Console\Commands;

use App\StoreViewsGTMetrix;
use Illuminate\Console\Command;
use App\Repositories\GtMatrixRepository;
use App\Repositories\GooglePageSpeedRepository;

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
        foreach ($gtMatrixURLs as $gtMatrixURL) {
            if (! $gtMatrixURL->account_id) {
                $gtMatrixURL->update(['status' => 'error', 'reason' => 'No gt-metrix account assoicated with this test']);

                continue;
            }
            app(GtMatrixRepository::class)->generateLog($gtMatrixURL);
            //Getting the record from google page speed
            app(GooglePageSpeedRepository::class)->generateReport($gtMatrixURL);
        }
    }
}
