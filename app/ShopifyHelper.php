<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Helpers\ProductHelper;
use App\Library\Shopify\Client as ShopifyClient;
class ShopifyHelper
{
    

    function __construct()
    {
        
    }

   

    public function pushProduct(Product $product)
    {
        // Check for product and session
        if ($product === null) {
            return false;
        }

                $productData = [
                    'product' => [
                        'body_html'       => $product->short_description,
                        'images'          => [],
                        'product_type'    => ($product->product_category && $product->category > 1) ? $product->product_category->title : "",
                        'published_scope' => 'web',
                        'title'           => $product->title,
                        'variants'        => [],
                        'vendor'          => ($product->brands) ? $product->brands->name : "",
                    ],
                ];

            // Add images to product
            if ($product->hasMedia(config('constants.attach_image_tag'))) {
                foreach ($product->getMedia(config('constants.attach_image_tag')) as $image) {
                    $productData['product']['images'][] = ['src' => $image->getUrl()];
                }
            }

            $productData['product']['variants'][] = [
                'barcode'              => (string) $product->id,
                'fulfillment_service'  => 'manual',
                'price'                => $product->price,
                'requires_shipping'    => true,
                'sku'                  => $product->sku,
                'title'                => (string) $product->title,
                'inventory_management' => 'shopify',
                'inventory_policy'     => 'deny',
                'inventory_quantity'   => $product->stock,
            ];

            $client = new ShopifyClient();
            $response = $client->addProduct($productData);

            $errors = [];
            if (!empty($response->errors)) {
                foreach ((array)$response->errors as $key => $message) {
                    foreach ($message as $msg) {
                        $errors[] = ucwords($key) . " " . $msg;
                    }
                }
            }

            if (!empty($errors)) {
                return response()->json(["code" => 500, "data" => $response, "message" => implode("<br>", $errors)]);
            }

            if (!empty($response->product)) {
                return $response->product;
            }

        return false;
    }
}