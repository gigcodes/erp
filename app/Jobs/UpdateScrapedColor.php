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

    public static function putLog($message)
    {
        \Log::channel('update_color_job')->info($message);

        return true;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        self::putLog("Job start time : ". date("Y-m-d H:i:s"));
        self::putLog("Params" . print_r([$this->product_id,$this->color],true));

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
            return;
        }


        if (isset($referencesColor)) {
            
            self::putLog("referencesColor : " . $referencesColor . " ||  color : ".$color);

            $productSupplier = $product->supplier;
            $supplier        = Supplier::where('supplier', $productSupplier)->first();
            $scrapedProducts = ScrapedProducts::where('website', $supplier->scraper->scraper_name)->get();

            self::putLog("Scrapeed Product Query time : ". date("Y-m-d H:i:s"));
            self::putLog("supplier : " . $productSupplier . " ||  Scraped Product Found : ".$scrapedProducts->count());

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

        self::putLog("Matched SKU : " . json_encode($scrapedProductSkuArray));

        //Update products with sku
        $totalUpdated = 0;
        if (count($scrapedProductSkuArray) != 0) {
            foreach ($scrapedProductSkuArray as $productSku) {
                self::putLog("Scrapeed Product {$productSku} update start time : ". date("Y-m-d H:i:s"));
                $oldProduct = Product::where('sku', $productSku)->first();
                if ($oldProduct != null) {
                    $oldProduct->color = $cat;
                    $oldProduct->save();
                    $totalUpdated++;
                    \App\ProductStatus::pushRecord($oldProduct->id,"MANUAL_COLOR");
                    self::putLog("Scrapeed Product {$productSku} update end time : ". date("Y-m-d H:i:s"));
                }
            }
        }

        \App\Notification::create([
            "role"       => "Admin",
            "message"    => $totalUpdated . " product has been affected while update color",
            "product_id" => $product->id,
            "user_id"    => 6,
        ]);

        self::putLog("Job end time : ". date("Y-m-d H:i:s"));

        return true;
    }
}
