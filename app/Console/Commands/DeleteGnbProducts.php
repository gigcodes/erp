<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\CronJobReport;
use Illuminate\Console\Command;
use App\Services\Scrap\GebnegozionlineProductDetailsScraper;

class DeleteGnbProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:gnb-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $scraper;

    /**
     * Create a new command instance.
     */
    public function __construct(GebnegozionlineProductDetailsScraper $scraper)
    {
        $this->scraper = $scraper;
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
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $this->scraper->deleteProducts();

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
