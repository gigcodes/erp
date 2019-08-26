<?php

namespace App\Console\Commands;

use App\Product;
use App\ScrapedProducts;
use Illuminate\Console\Command;

class UpdateInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the inventory in the ERP';

    /**
     * Create a new command instance.
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
        // Set empty array with SKU
        $arrInventory = [];

        // Update all products in database to inventory = 0
        Product::where('id', '>', 0)->update(['stock' => 0]);

        // Get all scraped products which are updated in the last 48 hours
        $scrapedProducts = ScrapedProducts::where('is_excel',0)->where('last_inventory_at', '>', date('Y-m-d H:i:s', time() - (2 * 86400)))->groupBy('sku')->selectRaw('COUNT(id) AS cnt, sku')->get();

        // Loop over scraped products
        foreach ($scrapedProducts as $scrapedProduct) {
            $arrInventory[$scrapedProduct->sku] = $scrapedProduct->cnt;
        }

        // Get all excel imported products which are updated in the last 14 days
        $scrapedProducts = ScrapedProducts::where('is_excel',1)->where('last_inventory_at', '>', date('Y-m-d H:i:s', time() - (14 * 86400)))->groupBy('sku')->selectRaw('COUNT(id) AS cnt, sku')->get();

        // Loop over excel imported products
        foreach ($scrapedProducts as $scrapedProduct) {
            if ( isset($arrInventory[$scrapedProduct->sku]) ) {
                $arrInventory[$scrapedProduct->sku] = $arrInventory[$scrapedProduct->sku] + $scrapedProduct->cnt;
            } else {
                $arrInventory[$scrapedProduct->sku] = $scrapedProduct->cnt;
            }
        }

        foreach ($scrapedProducts as $scrapedProduct) {
            // Find product
            $product = Product::where('sku', $scrapedProduct->sku)->first();

            // If product exists
            if ( $product !== NULL ) {
                $product->stock = $scrapedProduct->cnt;
                $product->save();
            }
        }

        // TODO: Update stock in Magento
    }
}
