<?php

namespace App\Http\Controllers;

use App\Customer;
use App\ReturnExchange;
use Illuminate\Http\Request;

class ReturnExchangeController extends Controller
{
    public function getOrders($id)
    {
        if (!empty($id)) {
            $customer  = Customer::find($id);
            $orderData = [];

            if (!empty($customer)) {
                $orders = $customer->orders;
                if (!empty($orders)) {
                    foreach ($orders as $order) {
                        $orderProducts = $order->order_product;

                        if (!empty($orderProducts)) {
                            foreach ($orderProducts as $orderProduct) {
                                $orderData[] = ['id' => $orderProduct->id];
                            }
                        }
                    }
                }
            }
        }

        $status   = ReturnExchange::STATUS;
        $response = (string)view("partials.return-exchange", compact('id', 'orderData', 'status'));

        return response()->json(["code" => 200, "html" => $response]);
    }

    /**
    * save the exchange result
    * @param Request
    * @param $id
    *
    **/
    public function save(Request $request, $id)
    {
        $params = $request->all();
        
        $returnExchange = \App\ReturnExchange::create($params);

        if($returnExchange) {

            // check if the order has been setup
            if(!empty($params["order_product_id"])) {
                $orderProduct = \App\OrderProduct::find($params["order_product_id"]);
                if(!empty($orderProduct) && !empty($orderProduct->product)) {
                    $product = $orderProduct->product; 
                }
            }

            // check if the product id is not stroed with order produc then 
            // check with product id
            if(empty($product)) {
                $product = \App\Product::find($params["product_id"]);
            }

            if(!empty($product)) {
                $returnExchangeProduct                    = new \App\ReturnExchangeProduct;
                $returnExchangeProduct->product_id        = $product->id;
                $returnExchangeProduct->order_product_id  = $params["order_product_id"];
                $returnExchangeProduct->name              = $product->name;
                $returnExchangeProduct->save();
            }
            // once return exchange created send message if request is for the return
            $returnExchange->notifyToUser();
        }




        return response()->json(["code" => 200 ,"data" => $returnExchange]);
    }
}
