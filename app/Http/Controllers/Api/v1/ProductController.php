<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Order;
use App\Product;
use App\ProductCancellationPolicie;
use App\StoreWebsiteOrder;
use Illuminate\Http\Request;
use \Carbon\Carbon;

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
        $product = \App\Product::where("sku", $sku)->first();

        if (empty($request->store_website) && empty($request->country_code)) {
            return response()->json(["code" => 500, "message" => "Country Code and Store Website id is required", "data" => []]);
        }

        if ($product) {
            $price              = $product->getPrice($request->store_website, $request->country_code);
            $price["sub_total"] = $price["total"];
            $price["duty"]      = $product->getDuty($request->store_website);
            $extra              = ($price["total"] * $price["duty"]) / 100;
            $price["total"]     = $price["total"] + $extra;

            $return = [
                "original_price" => $price["original_price"],
                "promotion"      => $price["promotion"],
                "sub_total"      => $price["sub_total"],
                "duty"           => $price["duty"],
                "total"          => number_format($price["total"], 2, ".", ""),
            ];

            return response()->json(["code" => 200, "message" => "Success", "data" => $return]);
        }

        return response()->json(["code" => 500, "message" => "Product not found in records", "data" => []]);
    }

    public function checkCancellation(Request $request)
    {
        if ($request->website) {
            $storeWebsite = \App\StoreWebsite::where("website", "like", $request->website)->first();
            // $get
            if ($storeWebsite) {
                $getStoreWebsiteOrder       = StoreWebsiteOrder::where('website_id', $storeWebsite->id)->where('platform_order_id', $request->order_id)->first();
                $ProductCancellationPolicie = ProductCancellationPolicie::where('store_website_id', $storeWebsite->id)->first();
                if ($getStoreWebsiteOrder) {
                    //check order status
                    $getOrder      = Order::with('order_product')->where('id', $getStoreWebsiteOrder->order_id)->first();
                    $order_product = $getOrder->order_product[0];
                    // print_r($order_product->toArray());
                    // $getOrderProduct =
                    if ($order_product != null) {
                        //$getCustomerOrderData = $getCustomerOrderData->where("swo.platform_order_id",$request->order_id);
                        $result_input               = $request->input();
                        $result_input["iscanceled"] = true;
                        $result_input["isrefund"]   = true;
                        $result_input["isreturn"]   = true;
                        if ($ProductCancellationPolicie) {
                            $order_product->purchase_status;
                            //if($order_product->purchase_status == "Cancel"){
                            $created    = new Carbon($order_product->created_at);
                            $now        = Carbon::now();
                            $difference = ($created->diff($now)->days < 1)
                            ? 'today'
                            : $created->diffInDays($now);
                            if ($difference > $ProductCancellationPolicie->days_cancelation) {
                                $result_input["iscanceled"] = false;
                                $result_input["isreturn"]   = false;
                            }
                            //}else if($order_product->purchase_status == "Refund to be processed"){
                            $created    = new Carbon($order_product->shipment_date);
                            $now        = Carbon::now();
                            $difference = ($created->diff($now)->days < 1)
                            ? 'today'
                            : $created->diffInDays($now);
                            if ($difference > $ProductCancellationPolicie->days_refund) {
                                $result_input["isrefund"] = false;
                                $result_input["isreturn"] = false;
                            }
                            // }
                            // if status is cancelled already then return false
                        }
                        if ($getOrder->order_status_id == 11) {
                            $result_input["iscanceled"] = false;
                            $result_input["isreturn"]   = false;
                        }

                        $message = $this->generate_erp_response("order.cancel.success", 0, $default = "Success", request('lang_code'));
                        return response()->json(["code" => 200, "message" => $message, "data" => $result_input]);
                    } else {
                        $message = $this->generate_erp_response("order.cancel.failed", 0, $default = "data not found", request('lang_code'));
                        return response()->json(["code" => 500, "message" => $message, "data" => []]);
                    }
                } else {
                    $message = $this->generate_erp_response("order.cancel.failed", 0, $default = "data not found", request('lang_code'));
                    return response()->json(["code" => 500, "message" => $message, "data" => []]);
                }
            } else {
                $message = $this->generate_erp_response("order.cancel.failed", 0, $default = "data not found", request('lang_code'));
                return response()->json(["code" => 500, "message" => $message, "data" => []]);
            }

        }
        $message = $this->generate_erp_response("order.cancel.failed.website_missing", 0, $default = "website is missing.", request('lang_code'));
        return response()->json(["code" => 500, "message" => $message, "data" => []]);
    }

    public function checkReturn(Request $request)
    {
        $result_input = ["has_return_request" => false];
        if ($request->website) {
            $storeWebsite = \App\StoreWebsite::where("website", "like", $request->website)->first();
            $sku          = explode("-", $request->product_sku);
            // $get
            if ($storeWebsite) {

                $returnExchange = StoreWebsiteOrder::where('website_id', $storeWebsite->id)->where('platform_order_id', $request->order_id)
                    ->join("orders as o", "o.id", "store_website_orders.order_id")
                    ->join("order_products as op", "op.order_id", "o.id")
                    ->join("return_exchange_products as rep", "rep.order_product_id", "op.id")
                    ->where("op.sku", $sku[0])
                    ->first();

                if ($returnExchange) {
                    $result_input = ["has_return_request" => true];
                }
                $message = $this->generate_erp_response("order.return-check.success", 0, $default = "Success", request('lang_code'));
                return response()->json(["code" => 200, "message" => $message, "data" => $result_input]);

            } else {
                $message = $this->generate_erp_response("order.return-check.failed", 0, $default = "data not found", request('lang_code'));
                return response()->json(["code" => 500, "message" => $message, "data" => []]);
            }

        }
        $message = $this->generate_erp_response("order.return-check.failed.website_missing", 0, $default = "website is missing.", request('lang_code'));
        return response()->json(["code" => 500, "message" => $message, "data" => []]);
    }
}
