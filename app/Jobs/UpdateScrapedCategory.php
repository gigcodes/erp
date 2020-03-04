<?php

namespace App\Jobs;

use App\Category;
use App\Product;
use App\ScrapedProducts;
use App\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateScrapedCategory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->product_id  = $params["product_id"];
        $this->category_id = $params["category_id"];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $product      = Product::find($this->product_id);
        $cat          = $this->category_id;
        $lastcategory = false;

        if ($product->scraped_products) {
            if (isset($product->scraped_products->properties) && isset($product->scraped_products->properties['category']) != null) {
                $category           = $product->scraped_products->properties['category'];
                $referencesCategory = implode(' ', $category);
                $lastcategory       = end($category);
            }
        } else {
            return response()->json(['success', 'Scrapped Product Doesnt Not Exist']);
        }

        if (isset($referencesCategory)) {

            $productSupplier = $product->supplier;
            $supplier        = Supplier::where('supplier', $productSupplier)->first();
            $scrapedProducts = ScrapedProducts::where('website', $supplier->scraper->scraper_name)->get();
            foreach ($scrapedProducts as $scrapedProduct) {
                if (isset($scrapedProduct->properties['category'])) {
                    $products = $scrapedProduct->properties['category'];
                    if (is_array($products)) {
                        $list = implode(' ', $products);
                        if (strtolower($referencesCategory) == strtolower($list)) {
                            $scrapedProductSkuArray[] = $scrapedProduct->sku;
                        }
                    }
                }
            }

            if (!isset($scrapedProductSkuArray)) {
                $scrapedProductSkuArray = [];
            }

            //Add reference to category
            $category = Category::find($cat);
            if ($lastcategory) {
                // find the current category and move its
                $refCat    = explode(",", $category->references);
                $refCat[]  = $lastcategory;
                $reference = implode(",", array_unique($refCat));

                // refrences updated
                $category->references = $reference;
                $category->save();
            }

            //Update products with sku
            if (count($scrapedProductSkuArray) != 0) {
                foreach ($scrapedProductSkuArray as $productSku) {
                    $oldProduct = Product::where('sku', $productSku)->first();
                    if ($oldProduct != null) {
                        $oldProduct->category = $cat;
                        $oldProduct->save();
                    }
                }
            }

            return response()->json(['success', 'Product Got Updated']);
        }
    }
}
