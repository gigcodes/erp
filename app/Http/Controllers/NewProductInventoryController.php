<?php

namespace App\Http\Controllers;

use App\Category;
use App\ColorReference;
use App\Library\Product\ProductSearch;
use App\Library\Shopify\Client as ShopifyClient;
use App\Stage;
use App\Supplier;
use Illuminate\Http\Request;

class NewProductInventoryController extends Controller
{
    public function __construct()
    {

    }

    public function index(Stage $stage)
    {
        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control'])->selected(request('category'))->renderAsDropdown();
        $suppliersDropList  = \Illuminate\Support\Facades\DB::select('SELECT id, supplier FROM suppliers INNER JOIN (
                                    SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
                                    ) as product_suppliers
                                ON suppliers.id = product_suppliers.supplier_id');
        $suppliersDropList = collect($suppliersDropList)->pluck("supplier", "id")->toArray();
        // $suppliersDropList = Supplier::where('supplier_status_id','1')->pluck('supplier','id')->toArray();

        $typeList = [
            "scraped"  => "Scraped",
            "imported" => "Imported",
            "uploaded" => "Uploaded",
        ];

        $params = request()->all();

        $products = (new ProductSearch($params))->getQuery()->paginate(24);

        $items = [];
        foreach ($products->items() as $product) {
            $date               = date("Y-m-d", strtotime($product->created_at));
            $referencesCategory = "";
            $referencesColor    = "";
            if (isset($product->scraped_products)) {
                // starting to see that howmany category we going to update
                if (isset($product->scraped_products->properties) && isset($product->scraped_products->properties['category']) != null) {
                    $category = $product->scraped_products->properties['category'];
                    if (is_array($category)) {
                        $referencesCategory = implode(' > ', $category);
                    }

                }

                if (isset($product->scraped_products->properties) && isset($product->scraped_products->properties['color']) != null) {
                    $referencesColor = $product->scraped_products->properties['color'];
                }
            }
            $product->reference_category = $referencesCategory;
            $product->reference_color    = $referencesColor;

            $supplier_list = '';
            foreach ($product->suppliers as $key => $supplier) {
                $supplier_list .= $supplier->supplier;
            }

            $product->supplier_list = $supplier_list;

            if (isset($items[$date])) {
                $items[$date][] = $product;
            } else {
                $items[$date] = [$product];
            }
        }

        // move to the function
        $categoryAll   = Category::where('parent_id', 0)->get();
        $categoryArray = [];
        foreach ($categoryAll as $category) {
            $categoryArray[] = array('id' => $category->id, 'value' => $category->title);
            $childs          = Category::where('parent_id', $category->id)->get();
            foreach ($childs as $child) {
                $categoryArray[] = array('id' => $child->id, 'value' => $category->title . ' > ' . $child->title);
                $grandChilds     = Category::where('parent_id', $child->id)->get();
                if ($grandChilds != null) {
                    foreach ($grandChilds as $grandChild) {
                        $categoryArray[] = array('id' => $grandChild->id, 'value' => $category->title . ' > ' . $child->title . ' > ' . $grandChild->title);
                    }
                }
            }
        }

        $categoryArray = collect($categoryArray)->pluck("value", "id")->toArray();
        $sampleColors  = ColorReference::select('erp_color')->groupBy('erp_color')->get()->pluck("erp_color", "erp_color")->toArray();

        return view("product-inventory.index", compact('category_selection', 'suppliersDropList', 'typeList', 'products', 'items', 'categoryArray', 'sampleColors'));
    }

    public function pushInShopify(Request $request, $id)
    {
        if ($id > 0) {

            $product = \App\Product::find($id);
            if (!empty($product)) {
                // Set data for Shopify
                $productData = [
                    'product' => [
                        'body_html'       => $product->short_description,
                        'images'          => [],
                        'product_type'    => ($product->product_category && $product->category > 1) ? $product->product_category->title : "",
                        'published_scope' => 'web',
                        'title'           => $product->name,
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
                    'barcode'             => (string) $product->id,
                    'fulfillment_service' => 'manual',
                    'price'               => $product->price,
                    'requires_shipping'   => true,
                    'sku'                 => $product->sku,
                    'title'               => (string) $product->name,
                ];

                $client   = new ShopifyClient();
                if($product->shopify_id) {
                    $response = $client->updateProduct($product->shopify_id,$productData);
                }else{
                    $response = $client->addProduct($productData);
                }

                $errors = [];
                if (!empty($response->errors)) {
                    if(is_array($response->errors)) {
                        foreach ($response->errors as $key => $message) {
                            foreach($message as $msg) {
                                $errors[] = ucwords($key) . " " . $msg;
                            }
                        }
                    }else{
                        $errors[] = $response->errors;
                    }
                }

                if (!empty($errors)) {
                    return response()->json(["code" => 500, "data" => [], "message" => implode("<br>", $errors)]);
                }

                if (!empty($response->product)) {
                    $product->shopify_id = $response->product->id;
                    $product->save();
                    return response()->json(["code" => 200, "data" => $response->product, "message" => "Success!"]);
            }

        }

    }

    return response()->json(["code" => 500, "data" => [], "message" => "Oops, Something went wrong!"]);
}
}
