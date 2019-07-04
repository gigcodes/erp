<?php

namespace App\Console\Commands;

use App\Product;
use Illuminate\Console\Command;

class FetchCompositionToProductsIfTheyAreScraped extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'composition:pull-if-in-scraped';

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
        Product::where('composition', '')->orWhereNull('composition')->chunk(1000, function ($products) {
            foreach ($products as $product) {
                dump('On -- ' . rand(555,5555));
                $scrapedProducts = $product->many_scraped_products;
                dump(count($scrapedProducts));
                if (!count($scrapedProducts)) {
                    continue;
                }

                foreach ($scrapedProducts as $scrapedProduct) {
                    $property = $scrapedProduct->properties;
                    $composition = $property['composition'] ?? '';
                    if ($composition) {
                        dump($composition);
                        $product->composition = $composition;
                        $product->save();
                        break;
                    }
                    $composition = $property['Details'] ?? '';
                    if ($composition) {
                        dump($composition);
                        $product->composition = $composition;
                        $product->save();
                        break;
                    }
                }

            }
        });
    }
}
