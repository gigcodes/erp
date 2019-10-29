<?php

namespace App\Console\Commands;

use App\Product;
use App\ScrapedProducts;
use App\CronJobReport;
use App\Services\Scrap\GetImagesBySku;
use Illuminate\Console\Command;

class GetProductImagesBySku extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sku:get-product-images';

    private $scraper;

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
    public function __construct(GetImagesBySku $getImagesBySku)
    {
        $this->scraper = $getImagesBySku;
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


        $this->scraper->scrap($this);

        $report->update(['end_time' => Carbon:: now()]);
    }
}
