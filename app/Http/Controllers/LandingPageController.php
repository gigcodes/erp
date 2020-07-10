<?php

namespace App\Http\Controllers;

use App\LandingPageProduct;
use App\Library\Shopify\Client as ShopifyClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        foreach ($records->items() as &$rec) {
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
                        ["product_id" => $productId, "name" => $product->name, "description" => $product->description, "price" => $product->price]
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
            if ($landingPageProduct) {
                $productData = [
                    'product' => [
                        'body_html'       => $landingPage->description,
                        'images'          => [],
                        'product_type'    => ($landingPageProduct->product_category && $landingPageProduct->category > 1) ? $landingPageProduct->product_category->title : "",
                        'published_scope' => 'web',
                        'title'           => $landingPage->name,
                        'variants'        => [],
                        'vendor'          => ($landingPageProduct->brands) ? $landingPageProduct->brands->name : "",
                    ],
                ];
            }

            // Add images to product
            if ($landingPageProduct->hasMedia(config('constants.attach_image_tag'))) {
                foreach ($landingPageProduct->getMedia(config('constants.attach_image_tag')) as $image) {
                    $productData['product']['images'][] = ['src' => $image->getUrl()];
                }
            }

            $productData['product']['variants'][] = [
                'barcode'              => (string) $landingPage->product_id,
                'fulfillment_service'  => 'manual',
                'price'                => $landingPage->price,
                'requires_shipping'    => true,
                'sku'                  => $landingPageProduct->sku,
                'title'                => (string) $landingPage->name,
                'inventory_management' => 'shopify',
                'inventory_policy'     => 'deny',
                'inventory_quantity'   => ($landingPage->stock_status == 1) ? $landingPageProduct->stock : 0,
            ];

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

}
