<?php

namespace App\Http\Controllers;

use App\Helpers\StatusHelper;
use App\LandingPageProduct;
use App\Library\Shopify\Client as ShopifyClient;
use App\Product;
use App\Services\Products\GraphqlService;
use App\StoreWebsite;
use App\StoreWiseLandingPageProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingPageController extends Controller
{
    const GALLERY_TAG_NAME = "gallery";

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $title  = "Landing Page";
        $status = \App\LandingPageProduct::STATUS;

        $landingPage = LandingPageProduct::first();

        return view("landing-page.index", compact(['title', 'status','store_websites']));
    }

    public function records(Request $request)
    {
        $records = \App\LandingPageProduct::join("products as p","p.id","landing_page_products.product_id");

        $keyword = request("keyword");
        if (!empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where("product_id", "LIKE", "%$keyword%");
            });
        }

        $stockStatus = request("stock_status");
        if($stockStatus != null) {
            $records = $records->where("landing_page_products.stock_status", $stockStatus);
        }

        $productStatus = request("product_status");
        if($productStatus != null) {
            $records = $records->where("p.status_id", $productStatus);
        }

        $status = request("status");
        if($status != null) {
            $records = $records->where("landing_page_products.status", $status);
        }

        $records = $records->select(["landing_page_products.*","p.status_id","p.stock"])->latest()->paginate();
//        dd($records);
        $store_websites = StoreWebsite::where('website_source','=','shopify')->get();

        $items = [];
        $allStatus = StatusHelper::getStatus();
        foreach ($records->items() as &$rec) {
            $landingPageProduct = $rec->product;
            if(!$landingPageProduct) {
                continue;
            }
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
                    $c = 0;
                    foreach($medias as $image) {
                        $temp = false;
                        if($c == 0){
                            $temp = true;
                        }
                        array_push($productData['images'], ['url' =>$image->getUrl(),'id'=>$image->id,'product_id'=>$landingPageProduct->id,'show' => $temp]);
                        $c++;
                    }
                }
            }
            $rec->images = $productData['images'];
            $rec->status_name = isset(\App\LandingPageProduct::STATUS[$rec->status]) ? \App\LandingPageProduct::STATUS[$rec->status] : $rec->status;
            $rec['stores'] = $store_websites;
            $rec->short_dec   = (strlen($rec->description) > 15) ? substr($rec->description, 0, 15).".." : $rec->description;
            $rec->short_dec   = utf8_encode($rec->short_dec);
            $items[]          = $rec;
        }

        return response()->json(["code" => 200, "data" => $items, "total" => $records->total(), "pagination" => (string) $records->render()]);
    }

    public function save(Request $request)
    {
        $params     = $request->all();
        $productIds = json_decode($request->get("images"), true);

        $errorMessage = [];

        if (!empty($productIds)) {
            foreach ($productIds as $productId) {
                $product = \App\Product::find($productId);
                if ($product) {
                    if($product->category > 3 && $product->hasMedia(config('constants.media_original_tag'))) {
                        // check status if not cropped then send to the cropper first
                        foreach ($product->getAllMediaByTag() as $tag => $medias) {
                            // if there is specific color then only send the images
                            if (strpos($tag, self::GALLERY_TAG_NAME) !== false) {
                                foreach ($medias as $image) {
                                    $image->delete();
                                }
                            }
                        }
                        $product->status_id = StatusHelper::$autoCrop;
                        $product->scrap_priority = 1;
                        // save product
                        $product->save();
                        \App\LandingPageProduct::updateOrCreate(
                            ["product_id" => $productId],
                            ["product_id" => $productId, "name" => $product->name, "description" => $product->short_description, "price" => $product->price]
                        );
                    }else{
                        $errorMessage[] = "Product has no category or images : ".$productId;
                    }
                }else{
                    $errorMessage[] = "Product not found : {$productId}";
                }
            }
        }

        if(count($errorMessage) > 0) {
            return redirect()->route('landing-page.index')->withError('There was some issue for given products : '.implode("<br>",$errorMessage));
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
        if (!empty($landingPage) && $landingPage->store_website_id > 0) {

            // if stock status exist then store it
            if ($request->stock_status != null) {
                $landingPage->stock_status = $request->stock_status;
                if ($landingPage->stock_status == 1) {
                    $landingPage->start_date = date("Y-m-d H:i:s");
                    $landingPage->end_date = date("Y-m-d H:i:s", strtotime($landingPage->start_date . ' + 1 days'));
                }
                $landingPage->save();
            }

            // Set data for Shopify
            $landingPageProduct = $landingPage->product;
            $productData = $landingPage->getShopifyPushData();


            if ($productData == false) {
                return response()->json(["code" => 500, "data" => "", "message" => "Pushing Failed: product is not approved"]);
            }

//            $productData = json_decode('{
//            "product":{"images":[],"product_type":"Dresses","published_scope":false,
//            "title":"ALEXANDER MCQUEEN ABITI",
//            "body_html":"Abito in misto viscosa-seta nero caratterizzato da girocollo, design smanicato, stampa grafica a contrasto, chiusura posteriore con cerniera, vestibilit\u00e0 aderente e tasglio corto.",
//            "variants":[{"barcode":"296563","fulfillment_service":"manual","requires_shipping":true,"sku":"622735Q1AOH1008",
//            "title":"ALEXANDER MCQUEEN ABITI","inventory_management":"shopify","inventory_policy":"deny","inventory_quantity":0,
//            "option1":"M","option2":"Asia Pasific","price":1890},{"barcode":"296563","fulfillment_service":"manual","requires_shipping":true,"sku":"622735Q1AOH1008",
//            "title":"ALEXANDER MCQUEEN ABITI","inventory_management":"shopify","inventory_policy":"deny","inventory_quantity":0,
//            "option1":"M","option2":"Asia","price":1890}],"vendor":"ALEXANDER McQUEEN","tags":"Home Page","published":false,
//            "options":[{"name":"sizes","values":["M"]},{"name":"country","values":["Asia Pasific","Asia"]}]}}', true);
            $client = new ShopifyClient();
            if ($landingPage->shopify_id) {
                $response = $client->updateProduct($landingPage->shopify_id, $productData, $landingPage->store_website_id);
            } else {
                $response = $client->addProduct($productData, $landingPage->store_website_id);
            }

            $errors = [];
            if (!empty($response->errors)) {
                foreach ((array)$response->errors as $key => $message) {
                    if (is_array($message)) {
                        foreach ($message as $msg) {
                            $errors[] = ucwords($key) . " " . $msg;
                        }
                    } else {
                        $errors[] = ucwords($key) . " " . $message;
                    }
                }
            }

            if (!empty($errors)) {
                return response()->json(["code" => 500, "data" => $response, "message" => implode("<br>", $errors)]);
            }

            if (!empty($response->product)) {
                $landingPage->shopify_id = $response->product->id;
                $landingPage->save();

                $selfProduct = Product::find($landingPage->product_id);

                if ($selfProduct) {

                    GoogleTranslateController::translateProductDetails($selfProduct);
                    $result = GraphqlService::sendTranslationByGrapql($landingPage->shopify_id, $landingPage->product_id);
                    GraphqlService::testGetDataByCurl($landingPage->shopify_id);

                    return response()->json(["code" => 200, "data" => $response->product, "message" => "Success!"]);
                } else {
                    return response()->json(["code" => 500, "data" => [], "message" => "Product not found."]);
                }
            }
        }

        return response()->json(["code" => 500, "data" => [], "message" => "Records not found or not store website assigned"]);

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

    public function changeStore(Request $request, $id)
    {
        $landing = \App\LandingPageProduct::find($id);

        if($landing && $request->get("store_website_id") != null) {
            $landing->store_website_id = $request->get("store_website_id");
            $landing->shopify_id = null;
            $landing->save();
            return response()->json(["code" => 200, "data" => "", "message" => "Success!"]);
        }else {
            return response()->json(["code" => 500, "data" => "", "message" => "Please select the store website!"]);
        }

    }

}
