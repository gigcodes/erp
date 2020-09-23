<?php

namespace App\Http\Controllers;

use App\Customer;
use App\ReturnExchange;
use App\Order;
use App\Product;
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
            $returnExchange->updateHistory();
        }

        return response()->json(["code" => 200, "data" => $returnExchange, "message" => "Request stored succesfully"]);
    }

    public function index(Request $request)
    {

        $returnExchange = ReturnExchange::latest('created_at')->paginate(10);

        return view("return-exchange.index",$returnExchange);
    }

    public function records(Request $request)
    {
        $params         = $request->all();
        $limit          = !empty($params["limit"]) ? $params["limit"] : 10;
        $returnExchange = ReturnExchange::leftJoin("return_exchange_products as rep", "rep.return_exchange_id", "return_exchanges.id")
                ->leftJoin("order_products as op", "op.id", "rep.order_product_id")
            ->leftJoin("customers as c", "c.id", "return_exchanges.customer_id")
            ->leftJoin("products as p", "p.id", "rep.product_id")
			->leftJoin("orders as o", "o.id", "rep.order_product_id")
			->leftJoin("store_website_orders as wo", "wo.id", "o.order_id")
			->leftJoin("store_websites as w", "w.id", "wo.website_id")
			->leftJoin("return_exchange_statuses as stat", "stat.id", "return_exchanges.status")
            ->latest('return_exchanges.created_at');

        if (!empty($params["customer_name"])) {
            $returnExchange = $returnExchange->where("c.name", "like", "%" . $params["customer_name"] . "%");
        }
		
		if (!empty($params["customer_email"])) {
            $returnExchange = $returnExchange->where("c.email", "like", "%" . $params["customer_email"] . "%");
        }
		
		if (!empty($params["customer_id"])) {
            $returnExchange = $returnExchange->where("c.id", $params["customer_id"]);
        }
		
		if (!empty($params["order_id"])) {
            $returnExchange = $returnExchange->where("o.order_id", $params["order_id"]);
        }

        if (!empty($params["order_number"])) {
            $returnExchange = $returnExchange->where("o.order_id", $params["order_number"]);
        }

        if (!empty($params["status"])) {
            $returnExchange = $returnExchange->where("return_exchanges.status", $params["status"]);
        }

        if (!empty($params["type"])) {
            $returnExchange = $returnExchange->where("return_exchanges.type", $params["type"]);
        }

        if (!empty($params["product"])) {
            $returnExchange = $returnExchange->where(function ($q) use ($params) {
                $q->orWhere("p.name", "like", "%" . $params["product"] . "%")
                    ->orWhere("p.id", "like", "%" . $params["product"] . "%")
                    ->orWhere("p.sku", "like", "%" . $params["product"] . "%");
            });
        }
		
		if (!empty($params["website"])) {
            $returnExchange = $returnExchange->where("w.title", "like", "%" . $params["website"] . "%");
        }

        $returnExchange = $returnExchange->select([
            "return_exchanges.*",
            "c.name as customer_name",
            "rep.product_id", "rep.name",
			"stat.status_name as status_name",
			"w.title as website"
        ])->paginate($limit);

        // update items for status
        $items = $returnExchange->items();
        foreach ($items as &$item) {
			$item["created_at_formated"] = date('d-m-Y', strtotime($item->created_at));
            //$item["status_name"] = @ReturnExchange::STATUS[$item->status];
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
            if($request->from == 'erp-customer') {
                return view('ErpCustomer::partials.edit-return-summery', compact('data'));
            }
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
            $returnExchange->updateHistory();
        }

        return response()->json(["code" => 200, "data" => [], "message" => "Request updated succesfully!!"]);
    }

    public function delete(Request $request, $id)
    {
        $ids = explode(",",$id);
		foreach($ids as $id)
		{
			$returnExchange = \App\ReturnExchange::find($id);
			if (!empty($returnExchange)) {
				// start to delete from here
				$returnExchange->returnExchangeProducts()->delete();
				$returnExchange->returnExchangeHistory()->delete();
				$returnExchange->delete();
			}
		}
        return response()->json(["code" => 200, "data" => [], "message" => "Request deleted succesfully!!"]);
    }

    public function history(Request $request, $id)
    {
        $result = \App\ReturnExchangeHistory::where("return_exchange_id",$id)->leftJoin("users as u","u.id","return_exchange_histories.user_id")
        ->select(["return_exchange_histories.*","u.name as user_name"])
        ->orderby("return_exchange_histories.created_at","desc")
        ->get();

        $history = [];
        if(!empty($result)) {
            foreach($result as $res) {
                $res["status"] = @ReturnExchange::STATUS[$res->status_id];
                $history[] = $res;
            }
        }

        return response()->json(["code" => 200, "data" => $history, "message" => ""]);       
    }

    public function getProducts($id)
    {
        if (!empty($id)) {
<<<<<<< HEAD
            $product  = \App\Product::find($id);
            if (!empty($product)) {
				
				$data[ 'dnf' ] = $product->dnf;
				$data[ 'id' ] = $product->id;
				$data[ 'name' ] = $product->name;
				$data[ 'short_description' ] = $product->short_description;
				$data[ 'activities' ] = $product->activities;
				$data[ 'scraped' ] = $product->scraped_products;
				
				$data[ 'measurement_size_type' ] = $product->measurement_size_type;
				$data[ 'lmeasurement' ] = $product->lmeasurement;
				$data[ 'hmeasurement' ] = $product->hmeasurement;
				$data[ 'dmeasurement' ] = $product->dmeasurement;
				
				$data[ 'size' ] = $product->size;
				$data[ 'size_value' ] = $product->size_value;
				
				$data[ 'composition' ] = $product->composition;
				$data[ 'sku' ] = $product->sku;
				$data[ 'made_in' ] = $product->made_in;
				$data[ 'brand' ] = $product->brand;
				$data[ 'color' ] = $product->color;
				$data[ 'price' ] = $product->price;
				$data[ 'status' ] = $product->status_id;
				
				$data[ 'euro_to_inr' ] = $product->euro_to_inr;
				$data[ 'price_inr' ] = $product->price_inr;
				$data[ 'price_inr_special' ] = $product->price_inr_special;
				
				$data[ 'isApproved' ] = $product->isApproved;
				$data[ 'rejected_note' ] = $product->rejected_note;
				$data[ 'isUploaded' ] = $product->isUploaded;
				$data[ 'isFinal' ] = $product->isFinal;
				$data[ 'stock' ] = $product->stock;
				$data[ 'reason' ] = $product->rejected_note;
				
				$data[ 'product_link' ] = $product->product_link;
				$data[ 'supplier' ] = $product->supplier;
				$data[ 'supplier_link' ] = $product->supplier_link;
				$data[ 'description_link' ] = $product->description_link;
				$data[ 'location' ] = $product->location;
				
				$data[ 'suppliers' ] = '';
				$data[ 'more_suppliers' ] = [];
				
				foreach ($product->suppliers as $key => $supplier) {
					if ($key == 0) {
						$data[ 'suppliers' ] .= $supplier->supplier;
					} else {
						$data[ 'suppliers' ] .= ", $supplier->supplier";
					}
				}
				
				$image = $product->getMedia(config('constants.media_tags'))->first();
				
				if($image !== NULL)
				{			
					$data[ 'images' ] = $image->getUrl();	
				}
				else
				{
					$data[ 'images' ] = "#";	
				}
							
				$data[ 'categories' ] = $product->category ? CategoryController::getCategoryTree($product->category) : '';	
				$data[ 'product' ] = $product;
			
                $response = (string) view("return-exchange.templates.productview", $data);
            }
        }       
        return response()->json(["code" => 200, "html" => $response]);
    }

    public function product(Request $request, $id) {
        if (!empty($id)) {
            $product = \App\Product::where("products.id", $id)
            ->leftJoin("order_products as op", "op.product_id", "products.id")
            ->leftJoin("orders", "orders.id", "op.order_id")
            ->leftJoin("brands", "brands.id", "products.brand")
            ->select(["orders.order_id as order_number", "brands.name as product_brand", "products.name as product_name",
                    "products.image as product_image", "products.price as product_price",
                    "products.supplier as product_supplier", "products.short_description as about_product"])
            ->get();
        }
        return response()->json(["code" => 200, "data" => $product, "message" => ""]);
    }
	
	public function updateCustomer(Request $request) {
		if($request->update_type == 1) {
			$ids = explode(",",$request->selected_ids);
			foreach($ids as $id) {
				$return = \App\ReturnExchange::where("id", $id)->first();
				if($return && $request->customer_message && $request->customer_message != "") {
					\App\Jobs\UpdateReturnExchangeStatusTpl::dispatch($return->id, $request->customer_message);
				}
			}
		}
		else {
			$ids = explode(",",$request->selected_ids);
			foreach($ids as $id) {
				if(!empty($id) && $request->customer_message && $request->customer_message != "" && $request->status) {
					$return = \App\ReturnExchange::where("id", $id)->first();
					$statuss = \App\ReturnExchangeStatus::where("id",$request->status)->first();
					if($return) {
						$return->status 	= $request->status;
						$return->save();
						\App\Jobs\UpdateReturnExchangeStatusTpl::dispatch($return->id,$request->customer_message);
					}
				}
			}
		}
		return response()->json(['message' => 'Successful'],200);
	}
	
	public function createStatus(Request $request) {
		$this->validate( $request, [
			'status_name' => 'required',
		] );
		$input = $request->except('_token');
		$isExist = \App\ReturnExchangeStatus::where('status_name',$request->status_name)->first();
		if(!$isExist) {
			\App\ReturnExchangeStatus::create([
    						'status_name'		=> $request->status_name
    					]);
			return response()->json(['message' => 'Successful'],200);
		}
		else {
			return response()->json(['message' => 'Fail'],401);
		}
	}
=======
            $order  = \App\Order::find($id);
            $orderData = [];

            if (!empty($order)) {
                $products = $order->order_product;
                if (!empty($products)) {
                    foreach ($products as $product) {
                        $pr = \App\Product::find($product->product_id);
                        if($pr) {
                        $orderData[] = ['id' => $product->id, 'name' => $pr->name];
                        }
                    }
                }
            }
        }
        $status   = ReturnExchange::STATUS;
        $id = $order->customer_id;
        $response = (string) view("partials.order-return-exchange", compact('id', 'orderData', 'status'));
        return response()->json(["code" => 200, "html" => $response]);
    }
>>>>>>> d02338110ec5250c590dfe020404630485177dcd
}
