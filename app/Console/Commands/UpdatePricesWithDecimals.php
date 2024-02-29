<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\CronJobReport;
use App\ScrapedProducts;
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
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $products = ScrapedProducts::where(function ($q) {
                $q->where('price', 'like', '%,%');
                $q->orWhere('price', 'not like', '%.%');
            })->get();

            foreach ($products as $key => $product) {
                $pmodel = \App\Product::find($product->product_id);
                if ($pmodel) {
                    $scPrice = str_replace('euro', '', $product->price);

                    $scPrice = preg_replace('/[^A-Za-z0-9\-]/', '', $scPrice);

                    $needToupdate = false;
                    if (strlen($scPrice) > 4 && strlen($scPrice) < 6) {
                        $scPrice      = substr($scPrice, 0, 3);
                        $scPrice      = $scPrice . '.00';
                        $needToupdate = true;
                    } elseif (strlen($scPrice) > 5 && strlen($scPrice) < 7) {
                        $scPrice      = substr($scPrice, 0, 4);
                        $scPrice      = $scPrice . '.00';
                        $needToupdate = true;
                    }

                    dump("$key - Scraped Product - $product->sku and needToupdate : " . $needToupdate);

                    if ($needToupdate) {
                        if (is_numeric($scPrice)) {
                            $scPrice = ceil($scPrice / 10) * 10;
                        }

                        $priceEurSpecial = 0;
                        $priceInrSpecial = 0;

                        $brand = \App\Brand::find($pmodel->brand_id);

                        // Check for EUR to INR
                        $priceInr = 0;
                        if (! empty($brand->euro_to_inr) && $brand) {
                            $priceInr = (float) $brand->euro_to_inr * (float) trim($scPrice);
                        } else {
                            $priceInr = (float) \App\Setting::get('euro_to_inr') * (float) trim($scPrice);
                        }

                        if (! empty($scPrice) && ! empty($priceInr) && $brand) {
                            $priceEurSpecial = $scPrice - ($scPrice * $brand->deduction_percentage) / 100;
                            $priceInrSpecial = $priceInr - ($priceInr * $brand->deduction_percentage) / 100;
                        }
                        $oldPrice = $pmodel->price;

                        $pmodel->price             = $scPrice;
                        $pmodel->price_inr         = $priceInr;
                        $pmodel->price_eur_special = $priceEurSpecial;
                        $pmodel->price_inr_special = $priceInrSpecial;

                        $pmodel->save();
                        $message = "$product->id has been updated with old price $oldPrice to new price $pmodel->price and inr price $pmodel->price_inr and eur special price is $pmodel->price_eur_special";

                        \Log::info($message);
                        $this->output->write($message);
                    }
                }
            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \Log::error($e);
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
