<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function price(Request $request, $sku)
    {
        $product = \App\Product::where("sku",$sku)->first();

        if (empty($request->store_website) && empty($request->country_code)) {
            return response()->json(["code" => 500, "message" => "Country Code and Store Website id is required", "data" => []]);
        }

        if ($product) {
            $price         = $product->getPrice($request->store_website, $request->country_code);
            $price["duty"] = $product->getDuty($request->store_website);

            return response()->json(["code" => 200, "message" => "Success", "data" => $price]);
        }

        return response()->json(["code" => 500, "message" => "Product not found in records", "data" => []]);
    }
}
