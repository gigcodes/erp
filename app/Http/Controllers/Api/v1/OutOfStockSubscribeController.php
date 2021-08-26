<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\OutOfStockSubscribe;
use App\Customer;
use App\StoreWebsite;
use App\WebsiteProduct;
use App\Product;

class OutOfStockSubscribeController extends Controller
{
   public function Subscribe(Request $request)
   {
    	$params =  request()->all();
		// validate incoming request
        $validator = Validator::make($params, [
           'email' => 'required',
           'sku' => 'required',
		   'website' => 'required'
       ]);

		if ($validator->fails()) {
            return response()->json(["code" => 500, "data" => $validator->messages(), "message"=>"Failed"]);
        }

		$storeWebsite = StoreWebsite::where("website","like",$request->website)->select('id')->first();      

		if($storeWebsite) {
			$website_id = $storeWebsite->id;
			$data=$params;
			$sku = explode("-",$request->get("sku"));
			$product =  Product::where("sku",$sku[0])->first();
			if($product) {
				$customer = Customer::where('email', $data['email'])->first();
				if($customer == null) {
					$customer = Customer::create(['name'=>$data['email'], 'email'=>$data['email'], 'store_website_id'=> $website_id]);
				}
				$status=0;
				\App\ErpLeads::create(['customer_id' => $customer->id,'lead_status_id' => 1,'product_id' => $product->id]);
				$arrayToStore = ['customer_id'=>$customer['id'], 'product_id'=>$product->id, 'status'=>$status, 'website_id'=>$website_id];
				OutOfStockSubscribe::updateOrCreate( ['customer_id'=>$customer['id'], 'product_id'=>$product->id, 'website_id'=>$website_id], $arrayToStore);
				
				 return response()->json(["code" => 'success', "message" => "Subscribed successfully."]);
			} else{
                return response()->json(["code" => 500 , "data" => [] , "message" => "Product not found in records"]);
            }
		}else{
            return response()->json(["code" => 500 , "data" => [] , "message" => "Website not found in records"]);
        }

       
   }
}
