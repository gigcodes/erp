<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Customer;
use App\Order;
use App\OrderProduct;
use App\ReturnExchangeProduct;
use App\ReturnExchange;
use App\StoreWebsiteOrder;

class BuyBackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:order_products,order_id',
            'product_sku' => 'required|exists:order_products,sku',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => 'Please check validation errors !', 'errors' => $validator->errors()], 400);
        }
        $getCustomerOrderData = StoreWebsiteOrder::Where('platform_order_id', $request->order_id)
            ->where('op.sku', $request->product_sku)
            ->join('orders as od', 'od.id', 'store_website_orders.order_id')
            ->join('order_products as op', 'op.order_id', 'od.id')
            ->join('products as p', 'p.sku', 'op.sku')
            ->select('p.name as product_name', 'op.product_price', 'op.sku', 'op.order_id', 'op.product_id','od.customer_id')
            ->first();
        if (!isset($getCustomerOrderData) || empty($getCustomerOrderData)) {
            return response()->json(['status' => 'failed', 'message' => 'No order found for the customer!'], 404);
        }
        $return_exchange_products_data = [
            "status_id" => 1, //Return request received from customer 
            "product_id" => $getCustomerOrderData->product_id,
            "order_product_id" => $getCustomerOrderData->product_id,
            "name" => $getCustomerOrderData->product_name
        ];
        $return_exchanges_data = [
            'customer_id' => $getCustomerOrderData->customer_id,
            'type' => 'buyback',
            'reason_for_refund' => 'buyback of product from order_products',
            'refund_amount' => $getCustomerOrderData->product_price,
            'status' => 1,
            'date_of_request' => date('Y-m-d H:i:s')
        ];

        $success = ReturnExchange::create($return_exchanges_data);
        if (!$success) {
            return response()->json(['status' => 'failed', 'message' => 'Unable to create buyback request!'], 500);
        }
        ReturnExchangeProduct::create($return_exchange_products_data);
        return response()->json(['status' => 'success', 'message' => 'buyback request created successfully'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function checkProductsForBuyback(request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => 'Please check validation errors !', 'errors' => $validator->errors()], 400);
        }
        $checkCustomer = Customer::where('email', $request->customer_email)->first();
        if (!isset($checkCustomer->email)) {
            return response()->json(['status' => 'failed', 'message' => 'Customer not found with this email !'], 404);
        }
        $customer_id = $checkCustomer->id;
        $getCustomerOrderData = Order::Where('customer_id', $customer_id)
            ->join('order_products as op', 'op.order_id', 'orders.id')
            ->join('products as p', 'p.id', 'op.product_id')
            ->join('store_website_orders as swo', 'swo.order_id', 'op.order_id')
            ->select('p.name as product_name', 'op.product_price', 'op.sku', 'op.product_id','swo.platform_order_id as order_id')
            ->get()->makeHidden(['action']);
        if (count($getCustomerOrderData) == 0) {
            return response()->json(['status' => 'failed', 'message' => 'No order found for the customer!'], 404);
        }
        $responseData = [];
        foreach($getCustomerOrderData as $getCustomerOrder){
            $responseData[$getCustomerOrder->order_id][] = $getCustomerOrder;
        }

        return response()->json(['status' => 'success', 'orders' => $responseData], 200);
    }
}
