<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\CronJobReport;
use Illuminate\Console\Command;
use App\Services\Scrap\WiseBoutiqueProductDetailsScraper;

class UpdateWiseProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:wise-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $scraper;

    /**
     * Create a new command instance.
     *
     * @param GebnegozionlineProductDetailsScraper $scraper
     */
    public function __construct(WiseBoutiqueProductDetailsScraper $scraper)
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
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $this->scraper->createProducts();
            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
