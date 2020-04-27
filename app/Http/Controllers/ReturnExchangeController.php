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
        $response = (string) view("partials.return-exchange", compact('id', 'orderData', 'status'));

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

        if ($returnExchange) {

            // check if the order has been setup
            if (!empty($params["order_product_id"])) {
                $orderProduct = \App\OrderProduct::find($params["order_product_id"]);
                if (!empty($orderProduct) && !empty($orderProduct->product)) {
                    $product = $orderProduct->product;
                }
            }

            // check if the product id is not stroed with order produc then
            // check with product id
            if (empty($product)) {
                $product = \App\Product::find($params["product_id"]);
            }

            if (!empty($product)) {
                $returnExchangeProduct                     = new \App\ReturnExchangeProduct;
                $returnExchangeProduct->product_id         = $product->id;
                $returnExchangeProduct->order_product_id   = $params["order_product_id"];
                $returnExchangeProduct->name               = $product->name;
                $returnExchangeProduct->return_exchange_id = $returnExchange->id;
                $returnExchangeProduct->save();
            }
            // once return exchange created send message if request is for the return
            $returnExchange->notifyToUser();
        }

        return response()->json(["code" => 200, "data" => $returnExchange, "message" => "Request stored succesfully"]);
    }

    public function index(Request $request)
    {

        //$returnExchange = ReturnExchange::latest('created_at')->paginate(10);

        return view("return-exchange.index");
    }

    public function records(Request $request)
    {
        $params         = $request->all();
        $limit          = !empty($params["limit"]) ? $params["limit"] : 10;
        $returnExchange = ReturnExchange::leftJoin("return_exchange_products as rep", "rep.return_exchange_id", "return_exchanges.id")
            ->leftJoin("customers as c", "c.id", "return_exchanges.customer_id")
            ->leftJoin("products as p", "p.id", "rep.product_id")
            ->latest('return_exchanges.created_at');

        if (!empty($params["customer_name"])) {
            $returnExchange = $returnExchange->where("c.name", "like", "%" . $params["customer_name"] . "%");
        }

        if (!empty($params["status"])) {
            $returnExchange = $returnExchange->where("return_exchanges.status", $params["status"]);
        }

        if (!empty($params["product"])) {
            $returnExchange = $returnExchange->where(function ($q) use ($params) {
                $q->orWhere("p.name", "like", "%" . $params["product"] . "%")
                    ->orWhere("p.id", "like", "%" . $params["product"] . "%")
                    ->orWhere("p.sku", "like", "%" . $params["product"] . "%");
            });
        }

        $returnExchange = $returnExchange->select([
            "return_exchanges.*",
            "c.name as customer_name",
            "rep.product_id", "rep.name",
        ])->paginate($limit);

        // update items for status
        $items = $returnExchange->items();
        foreach ($items as &$item) {
            $item["status_name"] = @ReturnExchange::STATUS[$item->status];
        }

        return response()->json([
            "code"       => 200,
            "data"       => $items,
            "pagination" => (string) $returnExchange->links(),
            "total"      => $returnExchange->total(),
            "page"       => $returnExchange->currentPage(),
        ]);
    }

    public function detail(Request $request, $id)
    {
        $returnExchange = ReturnExchange::find($id);
        //check error return exist
        if (!empty($returnExchange)) {
            $data["return_exchange"] = $returnExchange;
            $data["status"]          = ReturnExchange::STATUS;
            return response()->json(["code" => 200, "data" => $data]);
        }
        // if not found then add error response
        return response()->json(["code" => 500, "data" => []]);
    }

    public function update(Request $request, $id)
    {
        $params = $request->all();

        $returnExchange = \App\ReturnExchange::find($id);

        if (!empty($returnExchange)) {
            $returnExchange->fill($params);
            $returnExchange->save();
        }

        return response()->json(["code" => 200, "data" => [], "message" => "Request updated succesfully!!"]);
    }

    public function delete(Request $request, $id)
    {
        $returnExchange = \App\ReturnExchange::find($id);
        if (!empty($returnExchange)) {
            // start to delete from here
            $returnExchange->returnExchangeProducts()->delete();
            $returnExchange->delete();
        }
        return response()->json(["code" => 200, "data" => [], "message" => "Request deleted succesfully!!"]);
    }
}
