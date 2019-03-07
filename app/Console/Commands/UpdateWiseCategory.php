<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ScrapedProducts;
use App\Category;
use App\Product;

class UpdateWiseCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:wise-category';

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
      $products = ScrapedProducts::where('has_sku', 1)->where('website', 'Wiseboutique')->get();

      foreach ($products as $product) {
        if ($old_product = Product::where('sku', $product->sku)->first()) {
          $properties_array = $product->properties ?? [];

          if (array_key_exists('category', $properties_array)) {
            $categories = Category::all();
            $category_id = 1;

            foreach ($properties_array['category'] as $cat) {
              if ($cat == 'WOMAN') {
                $cat = 'WOMEN';
              }

              foreach ($categories as $category) {
                if (strtoupper($category->title) == $cat) {
                  $category_id = $category->id;
                }
              }
            }

            $old_product->category = $category_id;
            $old_product->save();
          }
        }
      }
    }
}
