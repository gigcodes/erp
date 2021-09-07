<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Customer;
use App\ErpLeads;
use App\Product;
use Carbon\Carbon;
use Auth;
use App\StoreWebsite;
use Illuminate\Support\Facades\Validator;
class CustomerController extends Controller
{

    public function __construct()
    {
        // $this->middleware('permission:customer');
    }

    // DEVTASK-20592 start
    public function add_cart_data(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'website' => 'required',
            'name' => 'required',
            'lang_code' => 'required',
            'item_info' => 'required|array',
            'item_info.*.sku' => 'required',
            'item_info.*.qty' => 'required',
        ]);
        if ($validator->fails()) {
            $message = $this->generate_erp_response("buyback.failed.validation",0, $default = "Please check validation errors !", request('lang_code'));
            return response()->json(['status' => 'failed', 'message' => $message, 'errors' => $validator->errors()], 400);
        }

        $responseData = [];

        $storeWebsite = \App\StoreWebsite::where("website","like",$request->website)->first();

        if($storeWebsite) {
            $checkCustomer = Customer::where('email', $request->email)
            ->where('store_website_id',$storeWebsite->id)
            ->first();
            if (!$checkCustomer) {
                $data =[
                    'name'=>$request->name,
                    'email'=>$request->email,
                    'store_website_id'=>$storeWebsite->id,
                ];
                $checkCustomer = Customer::create($data);
            }

            $customer_id = $checkCustomer->id;
            foreach($request->item_info as $item){
               $product = Product::where('sku',$item['sku'])->first();
               if($product){
                    $erp_lead = new ErpLeads;
                    $erp_lead->lead_status_id = 1;
                    $erp_lead->customer_id = $customer_id;
                    $erp_lead->product_id  = $product->id;
                    $erp_lead->category_id = $product->category;
                    $erp_lead->brand_id    = $product->brand;
                    $erp_lead->brand_segment = $product->brands ? $product->brands->brand_segment : null;
                    $erp_lead->color       = $checkCustomer->color;
                    $erp_lead->size        = $checkCustomer->size;
                    $erp_lead->gender      = $checkCustomer->gender;
                    $erp_lead->qty         = $item['qty'];
                    $erp_lead->min_price   = $product->price;
                    $erp_lead->max_price   = $product->price;;
                    $erp_lead->save();
               }
            }
        }

        return response()->json(['status' => 'success', 'orders' => $responseData], 200);
    }

    // DEVTASK-20592 end
}
