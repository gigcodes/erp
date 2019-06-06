<?php

namespace App\Console\Commands;

use App\CategoryMap;
use App\Product;
use App\ScrapedProducts;
use Illuminate\Console\Command;

class EnrichCategoryInProductsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encrich:category-on-products';

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
        $altList = CategoryMap::all();
        $self = $this;
        ScrapedProducts::chunk(5000, function($scrapProducts) use ($altList, $self) {
            foreach ($scrapProducts as $scrapProduct) {
                $productEntry = Product::where('sku', $scrapProduct->sku)->first();

                if (!$productEntry) {
                    continue;
                }

                $category = $productEntry->category;

                foreach ($altList as $item) {
                    $status = $self->doesCategoryExists($category, $item);

                    if ($status === true) {
                        break;
                    }

                }

            }
        });
    }

    private function doesCategoryExists($scrapedProductCategory, $alternativeList) {
        $alts = $alternativeList->alternatives;

    }

    private function gender() {

    }
}
