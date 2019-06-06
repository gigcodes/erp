<?php

namespace App\Console\Commands;

use App\ScrapedProducts;
use Illuminate\Console\Command;

class RecreateProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recreate:products-scraped';

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
      $products = ScrapedProducts::where('website', 'stilmoda')->get();
      // dd(count($products));
      foreach ($products as $product) {
        app('App\Services\Products\ProductsCreator')->createProduct($product);
      }
    }
}
