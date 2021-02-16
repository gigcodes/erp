<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    /**
    * @SWG\Get(
    *   path="/v1/product/{sku}/price",
    *   tags={"Product"},
    *   summary="Get product price through sku",
    *   operationId="get-product-price-through-sku",
    *   @SWG\Response(response=200, description="successful operation"),
    *   @SWG\Response(response=406, description="not acceptable"),
    *   @SWG\Response(response=500, description="internal server error"),
    *      @SWG\Parameter(
    *          name="mytest",
    *          in="path",
    *          required=true, 
    *          type="string" 
    *      ),
    * )
    *
    */
    public function price(Request $request, $sku)
    {
        $product = \App\Product::where("sku",$sku)->first();

        if (empty($request->store_website) && empty($request->country_code)) {
            return response()->json(["code" => 500, "message" => "Country Code and Store Website id is required", "data" => []]);
        }

        if ($product) {
            $price         = $product->getPrice($request->store_website, $request->country_code);
            $price["sub_total"] = $price["total"];
            $price["duty"] = $product->getDuty($request->store_website);
            $extra = ($price["total"] * $price["duty"]) / 100;
            $price["total"] = $price["total"] + $extra;

            $return = [
                "original_price" => $price["original_price"],
                "promotion" => $price["promotion"],
                "sub_total" => $price["sub_total"],
                "duty" => $price["duty"],
                "total" => number_format($price["total"],2,".",""),
            ];

            return response()->json(["code" => 200, "message" => "Success", "data" => $return]);
        }

        return response()->json(["code" => 500, "message" => "Product not found in records", "data" => []]);
    }
}
