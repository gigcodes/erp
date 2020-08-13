<?php

namespace App\Http\Controllers;

use App\Helpers\StatusHelper;
use App\LandingPageProduct;
use App\Library\Shopify\Client as ShopifyClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Plank\Mediable\Media;

class LandingPageController extends Controller
{

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $title  = "Landing Page";
        $status = \App\LandingPageProduct::STATUS;
        return view("landing-page.index", compact(['title', 'status']));
    }

    public function records(Request $request)
    {
        $records = \App\LandingPageProduct::query();

        $keyword = request("keyword");
        if (!empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where("product_id", "LIKE", "%$keyword%");
            });
        }

        $records = $records->paginate();

        $items = [];
        $allStatus = StatusHelper::getStatus();
        foreach ($records->items() as &$rec) {
            $landingPageProduct = $rec->product;
            if($landingPageProduct) {
                if(array_key_exists($landingPageProduct->status_id, $allStatus)){
                    $rec->productStatus = $allStatus[$landingPageProduct->status_id];
                }else{
                    $rec->productStatus = '';
                }
            }
            else {
                $rec->productStatus = '';
            }
           
            $productData['images'] = [];
            if ($landingPageProduct->hasMedia(config('constants.attach_image_tag'))) {
                foreach ($landingPageProduct->getAllMediaByTag() as $medias) {
                    foreach($medias as $image) {
                        array_push($productData['images'], ['url' =>$image->getUrl(),'id'=>$image->id,'product_id'=>$landingPageProduct->id]);
                    }
                }
            }
            $rec->images = $productData['images'];
            $rec->status_name = isset(\App\LandingPageProduct::STATUS[$rec->status]) ? \App\LandingPageProduct::STATUS[$rec->status] : $rec->status;
            $items[]          = $rec;
        }

        return response()->json(["code" => 200, "data" => $items, "total" => count($records), "pagination" => (string) $records->render()]);
    }

    public function save(Request $request)
    {
        $params     = $request->all();
        $productIds = json_decode($request->get("images"), true);

        if (!empty($productIds)) {
            foreach ($productIds as $productId) {
                $product = \App\Product::find($productId);
                if ($product) {
                    // check status if not cropped then send to the cropper first
                    if ($product->status_id != \App\Helpers\StatusHelper::$finalApproval) {
                        $product->scrap_priority = 1;
                    } else {
                        $product->scrap_priority = 0;
                    }
                    // save product
                    $product->save();
                    \App\LandingPageProduct::updateOrCreate(
                        ["product_id" => $productId],
                        ["product_id" => $productId, "name" => $product->name, "description" => $product->short_description, "price" => $product->price]
                    );
                }
            }
        }

        return redirect()->route('landing-page.index')->withSuccess('You have successfully added landing page!');
    }

    public function store(Request $request)
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'product_id' => 'required',
            'start_date' => 'required',
            'end_date'   => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = "";
            $messages     = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . "<br>";
                }
            }
            return response()->json(["code" => 500, "error" => $outputString]);
        }

        $id = $request->get("id", 0);

        $records = LandingPageProduct::find($id);

        if (!$records) {
            $records = new LandingPageProduct;
        }

        $records->fill($post);
        $records->save();

        return response()->json(["code" => 200, "data" => $records]);

    }

    /**
     * Edit Page
     * @param  Request $request [description]
     * @return
     */

    public function edit(Request $request, $id)
    {
        $landingPage = LandingPageProduct::where("id", $id)->first();

        if ($landingPage) {
            return response()->json(["code" => 200, "data" => $landingPage]);
        }

        return response()->json(["code" => 500, "error" => "Wrong row id!"]);
    }

    /**
     * delete Page
     * @param  Request $request [description]
     * @return
     */

    public function delete(Request $request, $id)
    {
        $landingPage = LandingPageProduct::where("id", $id)->first();

        if ($landingPage) {
            $landingPage->delete();
            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500, "error" => "Wrong row id!"]);
    }

    public function pushToShopify(Request $request, $id)
    {
        $landingPage = LandingPageProduct::where("id", $id)->first();

        if (!empty($landingPage)) {

            // if stock status exist then store it
            if ($request->stock_status != null) {
                $landingPage->stock_status = $request->stock_status;
                $landingPage->save();
            }

            // Set data for Shopify
            $landingPageProduct = $landingPage->product;
            if (! StatusHelper::isApproved($landingPageProduct->status_id) && $landingPageProduct->status_id != StatusHelper::$finalApproval) {
                return response()->json(["code" => 500, "data" => "", "message" => "Pushing Failed: product is not approved"]);
            }
            if ($landingPageProduct) {
                $productData = [
                    'product' => [
                        'images'          => [],
                        'product_type'    => ($landingPageProduct->product_category && $landingPageProduct->category > 1) ? $landingPageProduct->product_category->title : "",
                        'published_scope' => 'web',
                        'title'           => $landingPage->name,
                        'variants'        => [],
                        'vendor'          => ($landingPageProduct->brands) ? $landingPageProduct->brands->name : "",
                        'tags'            => 'flash_sales'
                    ],
                ];
            }

            // Add images to product
            if ($landingPageProduct->hasMedia(config('constants.attach_image_tag'))) {
                foreach ($landingPageProduct->getAllMediaByTag() as $tag => $medias) {
                    // if there is specific color then only send the images
                    if (strpos($tag, 'gallary_') !== false) {
                        foreach ($medias as $image) {
                            $productData['product']['images'][] = ['src' => $image->getUrl()];
                        }
                    }
                }
            }

            $generalOptions = [
                'barcode'              => (string) $landingPage->product_id,
                'fulfillment_service'  => 'manual',
                'requires_shipping'    => true,
                'sku'                  => $landingPageProduct->sku,
                'title'                => (string) $landingPage->name,
                'inventory_management' => 'shopify',
                'inventory_policy'     => 'deny',
                'inventory_quantity'   => ($landingPage->stock_status == 1) ? $landingPageProduct->stock : 0,
            ];

            if(!empty($landingPageProduct->size)) {
                $productSizes = explode(',', $landingPageProduct->size);
                $values = [];
                $sizeOptions = [];
                foreach ($productSizes as $size) {
                    array_push($values, (string)$size);
                    $sizeOptions[$size] = $landingPage->price;
                }
                $variantsOption = [
                    'name' => 'sizes',
                    'values' => $values
                ];
                $productData['product']['options'][] = $variantsOption;
            }

            
            $storeWebsite = \App\StoreWebsite::where("title","like","%o-labels%")->first();
            $countryGroupOptions = [];

            // setup for price
            $countryVariants = [];
            if($storeWebsite) {
                $countryGroups = \App\CountryGroup::all();
                if(!$countryGroups->isEmpty()) {
                    $countryList = [];
                    foreach ($countryGroups as $cg) {
                        array_push($countryList, (string)$cg->name);
                        $price = $landingPageProduct->getPrice($storeWebsite->id, $cg->id);
                        $firstCountry = $cg->groupItems->first();
                        // get the duty price of first country to see
                        $dutyPrice = 0;
                        if($firstCountry) {
                            $dutyPrice = $landingPageProduct->getDuty($firstCountry->country_code);
                        }
                        $countryGroupOptions[$cg->name] = $price['total'] + $dutyPrice;
                    }
                    $variantsOption = [
                        'name' => 'country',
                        'values' => $countryList
                    ];
                    $productData['product']['options'][] = $variantsOption;
                }
            }

            foreach($countryGroupOptions as $k => $v) {
                if(!empty($sizeOptions)) {
                    foreach($sizeOptions as $p => $d) {
                        $generalOptions["option1"]  = $p;
                        $generalOptions["option2"]  = $k;
                        $generalOptions["price"]    = $v;
                        $productData['product']['variants'][] = $generalOptions;
                    }
                }else{
                    $generalOptions["option1"]  = $p;
                    $generalOptions["price"]    = $v;
                    $productData['product']['variants'][] = $generalOptions;
                }
            }

            $client = new ShopifyClient();
            if ($landingPage->shopify_id) {
                $response = $client->updateProduct($landingPage->shopify_id, $productData);
            } else {
                $response = $client->addProduct($productData);
            }

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
                $landingPage->shopify_id = $response->product->id;
                $landingPage->save();
                return response()->json(["code" => 200, "data" => $response->product, "message" => "Success!"]);
            }

        }

        return response()->json(["code" => 500, "data" => [], "message" => "Records not found"]);

    }

    public function updateTime(Request $request)
    {
        $productIds = explode(',', $request->product_id);
        foreach ($productIds as $productId) {
            LandingPageProduct::where('product_id','=',$productId)->update(['start_date' => $request->start_date, 'end_date' => $request->end_date]);
        }
        return redirect()->back();
    }


    public function deleteImage($id, $productId)
    {
        \DB::table('mediables')->where('mediable_type', 'App\Product')
            ->where('media_id', $id)
            ->where('mediable_id', $productId)
            ->delete();
        return response()->json(["code" => 200, "data" => "", "message" => "Success!"]);
    }

}
