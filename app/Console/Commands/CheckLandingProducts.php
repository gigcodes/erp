<?php

namespace App\Console\Commands;

use App\Helpers\StatusHelper;
use App\Http\Controllers\LandingPageController;
use App\LandingPageProduct;
use App\StoreWiseLandingPageProducts;
use Illuminate\Console\Command;
use App\Library\Shopify\Client as ShopifyClient;

class CheckLandingProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:landing-page';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate landing products from shopify after end time';

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
        $client = new ShopifyClient();
        $landingProducts = LandingPageProduct::whereRaw('timestamp(end_date) < NOW()')->get();
        foreach ($landingProducts as $product) {
            $productData = [
                'product' => [
                    'published' => false,
                ],
            ];
            if ($product->shopify_id) {
                $response = $client->updateProduct($product->shopify_id, $productData);
            }
        }

        $landingProducts = LandingPageProduct::whereRaw('timestamp(start_date) < NOW() AND timestamp(end_date) > NOW()')->get();
        foreach ($landingProducts as $landingPage) {
                // Set data for Shopify
                $landingPageProduct = $landingPage->product;

                if (! StatusHelper::isApproved($landingPageProduct->status_id)) {
                    continue;
                }
                if ($landingPageProduct) {
                    $productData = [
                        'product' => [
                            'body_html' => $landingPage->description,
                            'images' => [],
                            'product_type' => ($landingPageProduct->product_category && $landingPageProduct->category > 1) ? $landingPageProduct->product_category->title : "",
                            'published_scope' => 'web',
                            'title' => $landingPage->name,
                            'variants' => [],
                            'vendor' => ($landingPageProduct->brands) ? $landingPageProduct->brands->name : "",
                            'tags' => 'flash_sales'
                        ],
                    ];
                }

                // Add images to product
                if ($landingPageProduct->hasMedia(config('constants.attach_image_tag'))) {
                    foreach ($landingPageProduct->getMedia(config('constants.attach_image_tag')) as $image) {
                        $productData['product']['images'][] = ['src' => $image->getUrl()];
                    }
                }

                $productSizes = explode(',', $landingPageProduct->size);
                $values = [];
                foreach ($productSizes as $size) {
                    array_push($values, (string)$size);
                    $productData['product']['variants'][] = [
                        'option1' => $size,
                        'barcode' => (string)$landingPage->product_id,
                        'fulfillment_service' => 'manual',
                        'price' => $landingPage->price,
                        'requires_shipping' => true,
                        'sku' => $landingPageProduct->sku,
                        'title' => (string)$landingPage->name,
                        'inventory_management' => 'shopify',
                        'inventory_policy' => 'deny',
                        'inventory_quantity' => ($landingPage->stock_status == 1) ? $landingPageProduct->stock : 0,
                    ];
                }
                $variantsOption = [
                    'name' => 'sizes',
                    'values' => $values
                ];
                $productData['product']['options'] = $variantsOption;

                //check landing page product related store website
                $store_wise_landing_page_products = StoreWiseLandingPageProducts::where(['landing_page_products_id' => $landingPageProduct->product_id])->get();
                if(count($store_wise_landing_page_products) > 0){
                    foreach($store_wise_landing_page_products as $store_products){
                        $client->addProduct($productData, $store_products->store_website_id);
                    }
                }else{
                    if ($landingPage->shopify_id) {
                        $response = $client->updateProduct($landingPage->shopify_id, $productData);
                    }
                }
        }

    }
}
