<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ScrapedProducts;
use App\Brand;
use App\Product;

class UpdateToryColor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:tory-color';

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
      $products = ScrapedProducts::where('has_sku', 1)->where('website', 'Tory')->get();

      foreach ($products as $product) {
        if ($old_product = Product::where('sku', $product->sku)->first()) {
          $properties_array = $product->properties;

          if (array_key_exists('color', $properties_array)) {
            $old_product->color = $properties_array['color'];
            $old_product->save();
          }
        }
      }
    }
}
