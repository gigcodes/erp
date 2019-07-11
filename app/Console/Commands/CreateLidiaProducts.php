<?php

namespace App\Console\Commands;

use App\ScrapedProducts;
use Illuminate\Console\Command;

class CreateLidiaProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:lidia-products';

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
      $scraped_products = ScrapedProducts::where('website', 'lidiashopping')->get();

      foreach ($scraped_products as $scraped_product) {
        app('App\Services\Products\LidiaProductsCreator')->createProduct($scraped_product);
      }
    }
}
