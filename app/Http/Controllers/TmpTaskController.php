<?php

namespace App\Http\Controllers;

use App\Product;
use App\StoreWebsite;
use App\Jobs\PushToMagento;
use Illuminate\Http\Request;
use App\Helpers\ProductHelper;
use App\Loggers\LogListMagento;
use App\Library\DHL\GetRateRequest;

class TmpTaskController extends Controller
{
    public function importLeads()
    {
        set_time_limit(0);
        $leads = \App\Leads::where('customer_id', '>', 0)->get();

        if (! $leads->isEmpty()) {
            foreach ($leads as $lead) {
                try {
                    $jsonBrand = json_decode($lead->multi_brand, true);
                    $jsonCategory = json_decode($lead->multi_category, true);

                    $jsonBrand = ! empty($jsonBrand) ? (is_array($jsonBrand) ? array_filter($jsonBrand) : [$jsonBrand]) : [];
                    $jsonCategory = ! empty($jsonCategory) ? (is_array($jsonCategory) ? array_filter($jsonCategory) : [$jsonCategory]) : [];

                    if ($lead->selected_product) {
                        $selectedProduct = json_decode($lead->selected_product, true);

                        $product = \App\Product::where('id', (is_array($selectedProduct) ? $selectedProduct[0] : $selectedProduct))->first();

                        if ($product) {
                            if (empty($jsonBrand)) {
                                $jsonBrand = [$product->brand];
                            }

                            if (empty($jsonCategory)) {
                                $jsonCategory = [$product->category];
                            }
                        }
                    }

                    $brandSegment = null;
                    if (! empty($jsonBrand)) {
                        $brand = \App\Brand::whereIn('id', $jsonBrand)->get();
                        if ($brand) {
                            $brandSegmentArr = [];
                            foreach ($brand as $v) {
                                $brandSegmentArr[] = $v->brand_segment;
                            }
                            $brandSegment = implode(',', array_unique($brandSegmentArr));
                        }
                    }

                    $erpLead = \App\ErpLeads::where([
                        'brand_id' => isset($jsonBrand[0]) ? $jsonBrand[0] : '',
                        'category_id' => isset($jsonCategory[0]) ? $jsonCategory[0] : '',
                        'customer_id' => $lead->customer_id,
                        'brand_segment' => $brandSegment,
                    ])->first();

                    if (! $erpLead) {
                        $erpLead = new \App\ErpLeads;
                    }

                    $erpLead->lead_status_id = $lead->status;
                    $erpLead->customer_id = $lead->customer_id;
                    $erpLead->product_id = ! empty($product) ? $product->id : null;
                    $erpLead->brand_id = isset($jsonBrand[0]) ? $jsonBrand[0] : null;
                    $erpLead->brand_segment = $brandSegment;
                    $erpLead->store_website_id = 15;
                    $erpLead->category_id = isset($jsonCategory[0]) ? $jsonCategory[0] : null;
                    $erpLead->color = null;
                    $erpLead->size = $lead->size;
                    $erpLead->min_price = 0.00;
                    $erpLead->max_price = 0.00;
                    $erpLead->type = 'import-leads';
                    $erpLead->created_at = $lead->created_at;
                    $erpLead->updated_at = $lead->updated_at;
                    $erpLead->save();

                    $mediaArr = $lead->getMedia(config('constants.media_tags'));
                    foreach ($mediaArr as $media) {
                        \DB::table('mediables')->where('media_id', $media->id)->where('mediable_type', \App\ErpLeads::class)->delete();
                        $erpLead->attachMedia($media, config('constants.media_tags'));
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    // do what you want here with $e->getMessage();
                }
            }
        }
    }

    public function importProduct()
    {
        $scraped_product = \App\ScrapedProducts::orderBy('id', 'desc')->first();
        app(\App\Services\Products\ProductsCreator::class)->createProduct($scraped_product);
    }

    public function testEmail(Request $request)
    {
        $orderSaved = \App\Order::find(2102);

        try {
            $email = \App\Email::create([
                'model_id' => $orderSaved->id,
                'model_type' => \App\Order::class,
                'from' => 'customercare@sololuxury.co.in',
                'to' => 'webreak.pravin@gmail.com',
                'subject' => 'TEST',
                'message' => 'Hello world',
                'template' => 'order-confirmation',
                'additional_data' => $orderSaved->id,
                'status' => 'pre-send',
                'is_draft' => 1,
            ]);

            \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');
        } catch (\Exception $e) {
            \Log::error($e);
            \Log::info('Order email was not send due to template not setup' . $orderSaved->id);
        }

        exit;

        $cnt = 'IN';
        $website = \App\StoreWebsite::find($request->get('store_website_id'));
        $product = \App\Product::find($request->get('product_id'));
        $dutyPrice = $product->getDuty($cnt);
        $discountPrice = $product->getPrice($website, $cnt, null, true, $dutyPrice);

        \Log::info(print_r($discountPrice, true));
        exit;

        $suggestion = \App\SuggestedProduct::first();
        echo '<pre>';
        print_r($suggestion);
        echo '</pre>';
        exit;

        SuggestedProduct::attachMoreProducts($suggestion);
        exit;
        //
        $order = \App\Order::latest()->first();

        if ($order) {
            $customer = $order->customer;
            $orderItems = $order->order_product;

            $data['order'] = $order;
            $data['customer'] = $customer;
            $data['orderItems'] = $orderItems;

            Mail::to('solanki7492@gmail.com')->send(new OrderInvoice($data));
        }
    }

    public function dhl(Request $request)
    {
        $rate = new GetRateRequest('soap');
        $result = $rate->call();
    }

    public function testPushProduct(Request $request)
    {
        $queueName = [
            '1' => 'mageone',
            '2' => 'magetwo',
            '3' => 'magethree',
        ];

        if ($request->product_id == null) {
            exit('Please Enter product id');
        }

        $productId = $request->product_id;
        if ($request->store_website_ids != null) {
            $websiteArrays = explode(',', $request->store_website_ids);
        }
        $product = \App\Product::find($request->product_id);

        // call product
        if ($product) {
            if (empty($websiteArrays)) {
                $websiteArrays = ProductHelper::getStoreWebsiteName($product->id);
            }
            if (count($websiteArrays) == 0) {
                \Log::channel('productUpdates')->info('Product started ' . $product->id . ' No website found');
                $msg = 'No website found for  Brand: ' . $product->brand . ' and Category: ' . $product->category;
            } else {
                $i = 1;
                foreach ($websiteArrays as $websiteArray) {
                    $website = StoreWebsite::find($websiteArray);
                    if ($website) {
                        // testing
                        \Log::channel('productUpdates')->info('Product started website found For website' . $website->website);
                        $log = LogListMagento::log($product->id, 'Start push to magento for product id ' . $product->id, 'info', $website->id);
                        //currently we have 3 queues assigned for this task.
                        if ($i > 3) {
                            $i = 1;
                        }
                        $log->queue = \App\Helpers::createQueueName($website->title);
                        $log->save();
                        PushToMagento::dispatch($product, $website, $log)->onQueue($log->queue);
                        $i++;
                    }
                }
            }
        }
    }

    public function fixBrandPrice()
    {
        $brands = \App\Brand::all();

        if (! $brands->isEmpty()) {
            foreach ($brands as $brand) {
                $isUpdatePrice = false;
                if (strlen($brand->min_sale_price) > 4) {
                    $isUpdatePrice = true;
                    echo "{$brand->name} updated from {$brand->min_sale_price} to " . substr($brand->min_sale_price, 0, 4);
                    echo '</br>';
                    $brand->min_sale_price = substr($brand->min_sale_price, 0, 4);
                }

                if (strlen($brand->max_sale_price) > 4) {
                    $isUpdatePrice = true;
                    echo "{$brand->name} updated from {$brand->max_sale_price} to " . substr($brand->max_sale_price, 0, 4);
                    echo '</br>';
                    $brand->max_sale_price = substr($brand->max_sale_price, 0, 4);
                }

                if ($isUpdatePrice) {
                    $brand->save();
                }
            }
        }
    }

    public function deleteChatMessages()
    {
        $limit = request('limit', 10000);
        $chatMessages = \App\ChatMessage::where('group_ids', '>', 0)->orderBy('created_at', 'asc')->limit($limit)->get();
        if (! $chatMessages->isEmpty()) {
            foreach ($chatMessages as $chatM) {
                $medias = $chatM->getAllMediaByTag();
                if (! $medias->isEmpty()) {
                    foreach ($medias as $i => $media) {
                        foreach ($media as $m) {
                            if (strpos($m->directory, 'product') === false) {
                                echo $m->getAbsolutePath() . ' started to delete';
                                $m->delete();
                            }
                        }
                    }
                }
            }
        }
    }

    public function deleteProductImages()
    {
        $limit = request('limit', 10000);
        $products = \App\Product::leftJoin('order_products as op', 'op.product_id', 'products.id')->where('stock', '<=', 0)
            ->where('supplier', '!=', 'in-stock')
            ->where('has_mediables', 1)
            ->havingRaw('op.product_id is null')
            ->groupBy('products.id')
            ->select(['products.*', 'op.product_id'])
            ->limit($limit)
            ->get();

        if (! $products->isEmpty()) {
            foreach ($products as $product) {
                $medias = $product->getAllMediaByTag();
                if (! $medias->isEmpty()) {
                    foreach ($medias as $i => $media) {
                        foreach ($media as $m) {
                            echo $m->getAbsolutePath() . ' started to delete';
                            $m->delete();
                        }
                    }
                }
                $product->has_mediables = 0;
                $product->save();
            }
        }
    }

    public function deleteQueue(Request $request)
    {
        \Redis::command('flushdb');
    }
}
