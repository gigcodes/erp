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
      // $products = ScrapedProducts::where('has_sku', 1)->where('website', 'G&B')->get();
      $products = ScrapedProducts::where('updated_at', '>', '2019-06-05 00:00')->get();
      // $products = ScrapedProducts::where('sku', '1190524')->get();

      // dd(count($products));
      foreach ($products as $key => $product) {
        dump("$key - Scraped Product - $product->sku");

        if ($old_product = Product::where('sku', $product->sku)->first()) {
          dump("$key - Product Found");

          $brand = Brand::find($product->brand_id);

          if (strpos($product->price, ',') !== false) {
            dump("$key - comma found");

            if (strpos($product->price, '.') !== false) {
              dump("$key - dot found");

              if (strpos($product->price, ',') < strpos($product->price, '.')) {
                dump("$key - comma first than dot");

                $final_price = str_replace(',', '', $product->price);
              } else {
                // $final_price = $product->price;
                $final_price = str_replace(',', '|', $product->price);
                $final_price = str_replace('.', ',', $final_price);
                $final_price = str_replace('|', '.', $final_price);
                $final_price = str_replace(',', '', $final_price);
              }
            } else {
              dump("$key - no dot found");
              $final_price = str_replace(',', '.', $product->price);
            }
          } else {
            dump("$key - no changes");
            $final_price = $product->price;
          }

          if (strpos($final_price, '.') !== false) {
            dump($final_price);
            $exploded = explode('.', $final_price);
            dump(json_encode($exploded));
            $replaced = trim(preg_replace('/[\&euro;€,eur]/i', '', $exploded[1]));
            dump(strlen($replaced));
            if (strlen($replaced) > 2) {
              dump("$key - has more than 2 digits after dot");
              $final_price = implode('', $exploded);
            }
          }

          // dd($final_price);

          $price = round(preg_replace('/[\&euro;€,eur]/i', '', $final_price));

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
