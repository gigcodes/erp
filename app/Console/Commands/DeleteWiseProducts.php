<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CronJobReport;
use App\Services\Scrap\WiseBoutiqueProductDetailsScraper;


class DeleteWiseProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:wise-products';

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
        $report = CronJobReport::create([
        'signature' => $this->signature,
        'start_time'  => Carbon::now()
        ]);

        $this->scraper->deleteProducts();

        $report->update(['end_time' => Carbon:: now()]);
    }
}
