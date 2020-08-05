<?php

namespace App;

use App\Customer;
use App\Order;
use App\StoreWebsiteOrder;
use App\Helpers\OrderHelper;
use App\OrderProduct;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Helpers\ProductHelper;
use App\Library\Shopify\Client as ShopifyClient;
use seo2websites\MagentoHelper\MagentoHelperv2 as MagentoHelper;
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

    /**
     * Method to sync shopify orders to ERP orders. We'll receive shopify order though a webhook
     * Ref: https://shopify.dev/docs/admin-api/rest/reference/events/webhook?api[version]=2020-07
     *
     * @author Sukhwinder Singh
     * @param [type] $store_id
     * @param [type] $order
     * @return void
     */
    public static function syncShopifyOrders($store_id, $order)
    {

        // \Log::info(print_r($order,true));

        //Checking in order table
        $checkIfOrderExist = StoreWebsiteOrder::where('order_id', $order["id"])->where('website_id', $store_id)->first();

        //Checking in Website Order Table
        if ($checkIfOrderExist) {
            return;
        }

        $balance_amount = 0;
        $shopify_order_id = $order["id"];

        // Check for customer details out of order
        $firstName      = isset($order["customer"])? (isset($order["customer"]["first_name"]) ? $order["customer"]["first_name"] : "N/A") : "N/A";
        $lastName       = isset($order["customer"])? (isset($order["customer"]["last_name"]) ? $order["customer"]["last_name"] : "N/A") : "N/A";

        $full_name      = $firstName . ' ' . $lastName;
        $customer_phone = isset($order["customer"])? (isset($order["customer"]["phone"]) ? $order["customer"]["phone"] : '') : '';

        $customer = Customer::where('email', $order["customer"]["email"])->first();

        // Create a customer if doesn't exists
        if (!$customer) {
            $customer = new Customer;
        }

        $customer->name    = $full_name;
        $customer->email   = $order["customer"]["email"];
        $customer->address = $order["billing_address"]["address1"];
        $customer->city    = $order["billing_address"]["city"];
        $customer->country = $order["billing_address"]["country"];
        $customer->pincode = $order["billing_address"]["zip"];
        $customer->phone   = $order["billing_address"]["phone"];
        $customer->save();

        $customer_id    = $customer->id;
        $order_status   = '';
        $payment_method = '';

        // For shopify payment method will always by shopify_payments
        $payment_method = 'shopify_payments';

        // check the processing method and convert it to generic method name used by ERP
        if ($order["financial_status"] == 'paid') {
            $order_status = OrderHelper::$purchaseComplete;
        } else {
            $order_status = OrderHelper::$pendingPurchase;
        }


        $id = \DB::table('orders')->insertGetId(
            array(
                'customer_id'     => $customer_id,
                'order_id'        => $order["id"],
                'order_type'      => 'online',
                'order_status'    => $order_status,
                'order_status_id' => $order_status,
                'payment_mode'    => $payment_method,
                'order_date'      => $order["created_at"],
                'client_name'     => $full_name,
                'city'            => $order["billing_address"]["city"],
                'advance_detail'  => 0,
                'contact_detail'  => $order["billing_address"]["phone"],
                'balance_amount'  => $balance_amount,
                'created_at'      => $order["created_at"],
                'updated_at'      => $order["created_at"],
            ));

        $items = $order["line_items"];
        foreach ($items as $item) {
            if (round($item["price"]) > 0) {

                //
                $size = '';

                // We already have a helper function to get the product attributes
                $skuAndColor = MagentoHelper::getSkuAndColor($item["sku"]);

                // Store products per order
                DB::table('order_products')->insert(
                    array(
                        'order_id'      => $id,
                        'product_id'    => !empty($skuAndColor['product_id']) ? $skuAndColor['product_id'] : null,
                        'sku'           => $skuAndColor['sku'],
                        'product_price' => round($item["price"]),
                        'qty'           => round($item["quantity"]),
                        'size'          => $size,
                        'color'         => $skuAndColor['color'],
                        'created_at'    => $order["created_at"],
                        'updated_at'    => $order["created_at"],
                    )
                );
            }
        }
        $orderSaved = Order::find($id);


        //Store Order Id Website ID and Shopify ID

        $websiteOrder                   = new StoreWebsiteOrder();
        $websiteOrder->website_id       = $store_id;
        $websiteOrder->status_id        = $order_status;
        $websiteOrder->order_id         = $orderSaved->id;
        $websiteOrder->platform_order_id = $shopify_order_id;
        $websiteOrder->save();

        \Log::info("Saved order: ".$orderSaved->id);

    }

    /**
     * Method to sync shopify customers to ERP customers. We'll receive shopify customer though a webhook
     * Ref: https://shopify.dev/docs/admin-api/rest/reference/events/webhook?api[version]=2020-07
     *
     * @author Sukhwinder Singh
     * @param [type] $store_id
     * @param [type] $customer
     * @return void
     */
    public static function syncShopifyCustomers($store_id, $store_customer){

        // \Log::info(print_r($store_customer,true));

        // Extract customer details from the payload
        $firstName      = isset($store_customer)? (isset($store_customer["first_name"]) ? $store_customer["first_name"] : "N/A") : "N/A";
        $lastName       = isset($store_customer)? (isset($store_customer["last_name"]) ? $store_customer["last_name"] : "N/A") : "N/A";

        $full_name      = $firstName . ' ' . $lastName;
        $customer_phone = isset($store_customer)? (isset($store_customer["phone"]) ? $store_customer["phone"] : '') : '';
        $customer_address = isset($store_customer["addresses"]["address1"])? (isset($store_customer["addresses"]["address1"]) ? $store_customer["phone"] : '') : '';
        $customer_city = isset($store_customer["address1"])? (isset($store_customer["address1"]["city"]) ? $store_customer["address1"]["city"] : '') : '';
        $customer_country = isset($store_customer["address1"])? (isset($store_customer["address1"]["country"]) ? $store_customer["address1"]["country"] : '') : '';
        $customer_zip = isset($store_customer["address1"])? (isset($store_customer["address1"]["zip"]) ? $store_customer["address1"]["zip"] : '') : '';
        $customer_phone = isset($store_customer)? (isset($store_customer["phone"]) ? $store_customer["phone"] : '') : '';

        $customer = Customer::where('email', $store_customer["email"])->first();

        // Create a customer if doesn't exists
        if (!$customer) {
            $customer = new Customer;
        }

        $customer->name    = $full_name;
        $customer->email   = $store_customer["email"];
        $customer->address = $customer_address;
        $customer->city    = $customer_city;
        $customer->country = $customer_country;
        $customer->pincode = $customer_zip;
        $customer->phone   = $customer_phone;
        $customer->save();

        \Log::info("Saved customer: ".$customer->id);

    }

    public static function validateShopifyWebhook($data, $secret, $hmac_header){

        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $secret, true));
        return hash_equals($hmac_header, $calculated_hmac);

    }




}