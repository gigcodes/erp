<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ScrapedProducts;
use App\Brand;
use App\Product;

class UpdateGnbPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:gnb-price';

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
      $products = ScrapedProducts::where('has_sku', 1)->where('website', 'G&B')->get();

      foreach ($products as $product) {
        if ($old_product = Product::where('sku', $product->sku)->first()) {
          $brand = Brand::find($product->brand_id);

          $price = round(preg_replace('/[\&euro;€.]/', '', $product->price));
          $old_product->price = $price;
          if(!empty($brand->euro_to_inr))
            $old_product->price_inr = $brand->euro_to_inr * $old_product->price;
          else
            $old_product->price_inr = Setting::get('euro_to_inr') * $old_product->price;

          $old_product->price_inr = round($old_product->price_inr, -3);
          $old_product->price_special = $old_product->price_inr - ($old_product->price_inr * $brand->deduction_percentage) / 100;

          $old_product->price_special = round($old_product->price_special, -3);

          $old_product->save();
        }
      }
    }
}
