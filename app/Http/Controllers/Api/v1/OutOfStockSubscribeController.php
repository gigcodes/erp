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
           'product_id' => 'required',
		   'website' => 'required'
       ]);

		if ($validator->fails()) {
            return response()->json(["code" => 500, "data" => $validator->messages(), "message"=>"Failed"]);
        }

		$storeWebsite = StoreWebsite::where("website","like",$request->website)->select('id')->first();      

		if($storeWebsite) {
			 $website_id = $storeWebsite->id;
			$data=$params;

			$customer = Customer::where('email', $data['email'])->first();
			if($customer == null) {
				$customer = Customer::create(['name'=>$data['email'], 'email'=>$data['email'], 'store_website_id'=> $website_id]);
			}
			$status=0;
			\App\ErpLeads::create(['customer_id' => $customer->id,'lead_status_id' => 1,'product_id' => $data['product_id']]);
			$arrayToStore = ['customer_id'=>$customer['id'], 'product_id'=>$data['product_id'], 'status'=>$status];
			OutOfStockSubscribe::updateOrCreate( ['customer_id'=>$customer['id'], 'product_id'=>$data['product_id']], $arrayToStore);
		}
        return response()->json(["code" => 'success', "message" => "Subscribed successfully."]);
   }
}
