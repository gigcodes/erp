<?php

namespace App\Console\Commands;

use App\CronJobReport;
use App\Product;
use App\ScrapedProducts;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdatePricesWithDecimals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:price-decimals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update scrapped products prices with decimals';

    private $scraper;

    /**
     * Create a new command instance.
     *
     * @param GebnegozionlineProductDetailsScraper $scraper
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
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $products = ScrapedProducts::where(function ($q) {
                $q->where('price', 'like', '%,%');
                $q->orWhere('price', 'not like', '%.%');
            })->get();

            foreach ($products as $key => $product) {

                dump("$key - Scraped Product - $product->sku");

                $scPrice = str_replace('euro', '', $product->price);

                $scPrice = preg_replace('/[^A-Za-z0-9\-]/', '', $scPrice);

                if (strlen($scPrice) > 4 && strlen($scPrice) < 6) {
                    $scPrice = substr($scPrice, 0, 3);
                    $scPrice = $scPrice . ".00";
                } elseif (strlen($scPrice) > 5 && strlen($scPrice) < 7) {
                    $scPrice = substr($scPrice, 0, 4);
                    $scPrice = $scPrice . ".00";
                }

                if (is_numeric($scPrice)) {
                    $scPrice = ceil($scPrice / 10) * 10;
                }

                $product->price = $scPrice;
                $product->save();

            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
