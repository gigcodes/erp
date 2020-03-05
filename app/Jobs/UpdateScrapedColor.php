<?php

namespace App\Jobs;

use App\Product;
use App\ScrapedProducts;
use App\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateScrapedColor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $params;
    public $product_id;
    public $color;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->product_id = $params["product_id"];
        $this->color      = $params["color"];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $product      = Product::find($this->product_id);
        $cat          = $this->color;
        $lastcategory = false;
        if($product) {
            $scrapedProductSkuArray[] = $product->sku; 
        }

        if ($product->scraped_products) {
            if (isset($product->scraped_products->properties) && isset($product->scraped_products->properties['colors']) != null) {
                $color           = $product->scraped_products->properties['colors'];
                $referencesColor = $color;
            }
            if (isset($product->scraped_products->properties) && isset($product->scraped_products->properties['color']) != null) {
                $color           = $product->scraped_products->properties['color'];
                $referencesColor = $color;
            }
        } else {
            return response()->json(['success', 'Scrapped Product Doesnt Not Exist']);
        }

        if (isset($referencesColor)) {

            $productSupplier = $product->supplier;
            $supplier        = Supplier::where('supplier', $productSupplier)->first();
            $scrapedProducts = ScrapedProducts::where('website', $supplier->scraper->scraper_name)->get();

            foreach ($scrapedProducts as $scrapedProduct) {
                if (isset($scrapedProduct->properties['colors'])) {
                    $colors = $scrapedProduct->properties['colors'];
                    if (strtolower($referencesColor) == strtolower($colors)) {
                        $scrapedProductSkuArray[] = $scrapedProduct->sku;
                    }

                }
                if (isset($scrapedProduct->properties['color'])) {
                    $colors = $scrapedProduct->properties['color'];
                    if (strtolower($referencesColor) == strtolower($colors)) {
                        $scrapedProductSkuArray[] = $scrapedProduct->sku;
                    }
                }
            }

            if (!isset($scrapedProductSkuArray)) {
                $scrapedProductSkuArray = [];
            }
        }

        //Update products with sku
        $totalUpdated = 0;
        if (count($scrapedProductSkuArray) != 0) {
            foreach ($scrapedProductSkuArray as $productSku) {
                $oldProduct = Product::where('sku', $productSku)->first();
                if ($oldProduct != null) {
                    $oldProduct->color = $cat;
                    $oldProduct->save();
                    $totalUpdated++;
                }
            }
        }

        \App\Notification::create([
            "role"       => "Admin",
            "message"    => $totalUpdated . " product has been affected while update color",
            "product_id" => $product->id,
            "user_id"    => 6,
        ]);

        return response()->json(['success', 'Product Got Updated']);
    }
}
