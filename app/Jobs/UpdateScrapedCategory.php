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
    public $product_id;
    public $category_id;

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
            $totalUpdated = 0;
            if (count($scrapedProductSkuArray) != 0) {
                $scrapedProductSkuArray = array_unique($scrapedProductSkuArray);
                foreach ($scrapedProductSkuArray as $productSku) {
                    $oldProduct = Product::where('sku', $productSku)->first();
                    if ($oldProduct != null) {
                        $oldProduct->category = $cat;
                        $oldProduct->save();
                        $totalUpdated++;
                    }
                }
            }

            \App\Notification::create([
                "role" => "Admin",
                "message" => $totalUpdated." product has been affected while update category",
                "product_id" => $product->id,
                "user_id" => 49
            ]);

            return response()->json(['success', 'Product Got Updated']);
        }
    }
}
