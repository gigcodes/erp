<?php

namespace App\Http\Controllers\product_price;

use App\Http\Controllers\Controller;
use App\CountryGroup;
use App\StoreWebsite;
use App\WebsiteStore;
use App\Product;
use App\Setting;
use App\Brand;
use App\Supplier;
use App\SimplyDutyCountry;
use App\Helpers\StatusHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ProductPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        ini_set("memory_limit", -1);

        $countryGroups = SimplyDutyCountry::getSelectList();
        $cCodes = $countryGroups;
        if(!empty($request->country_code)) {
            $cCodes = SimplyDutyCountry::where("country_code",$request->country_code)->pluck("country_name","country_code")->toArray();//$request->country_code;
        }

        $product_list = [];
        $suppliers = [];
        $brands = [];
        $websites = StoreWebsite::where("is_published","1")->get()->pluck('title', 'id')->toArray();
        $storeWebsites = StoreWebsite::select('title', 'id','website')->where("is_published","1");
        if($request->websites && !empty($request->websites)){
            $storeWebsites = $storeWebsites->whereIn('id', $request->websites);
        } 
        $storeWebsites = $storeWebsites->get()->toArray();
        // if(strtolower($request->random) == "yes" && empty($request->product)) {
        //     $products = Product::where('status_id', StatusHelper::$finalApproval)->groupBy('category'); 
        // }else{
        //     $products = Product::where('id', $request->product )->orWhere( 'sku', $request->product);
        // }
        // if($request->suppliers && !empty($request->suppliers)){
        //     $products = $products->whereHas('suppliers', function($query) use ($request){
        //         $query->whereIn('supplier_id', $request->suppliers);
        //     });
        // }
        // if($request->brands && !empty($request->brands)){
        //     $products = $products->whereHas('brands', function($query) use ($request){
        //         $query->whereIn('id', $request->brands);
        //     });
        // } 
    	$filter_data = $request->input();
		$products = \App\Product::getProducts($filter_data, 0);
        if($request->ajax()){
    		$products = \App\Product::getProducts($filter_data, $request->page - 1);
        }
        $selected_brands = null;
		if($request->brand_names){
            $selected_brands = Brand::select('id','name')->whereIn('id',$request->brand_names)->get();
		}
        
		$selected_suppliers = null;
		if($request->supplier){
            $selected_suppliers = Supplier::select('id','supplier')->whereIn('id',$request->supplier)->get();
		}
        
		$selected_websites = null;
		if($request->websites){
            $selected_websites = StoreWebsite::select('id','title')->whereIn('id',$request->websites)->get();
		}

        if(!count($products)){
            // return redirect()->back()->with('error','No product found');
        }

        foreach ($storeWebsites as $key => $value) {
            foreach($products as $product) { 
                foreach($cCodes as $ckey => $cco) {
                    $dutyPrice = $product->getDuty( $ckey );
                    
                    $price = $product->getPrice( $value['id'], $ckey,null, true,$dutyPrice, null, null, null, isset($product->suppliers_info) ?  $product->suppliers_info[0]->price : 0, $product->category_segment);
                    $ivaPercentage = \App\Product::IVA_PERCENTAGE;
                    $productPrice = number_format($price['original_price'],2,'.','');
                    $product_list[] = [
                        'storeWebsitesID'      => $value['id'],
                        'getPrice'             => $price,
                        'id'                   => $product->id,
                        'sku'                  => $product->sku,
                        // 'brand'                => ($product->brands) ? $product->brands->name : "-",
                        'brand'                => isset($product->brand_name) ? $product->brand_name : "-" ,
                        // 'segment'              => ($product->brands) ? $product->brands->brand_segment : "-",
                        'segment'              => $product->category_segment != null ? $product->category_segment : "-",
                        'website'              => $value['website'],
                        'eur_price'            => $productPrice,
                        'seg_discount'         => (float)$price['segment_discount'],
                        'segment_discount_per' => (float)$price['segment_discount_per'],
                        'iva'                  => \App\Product::IVA_PERCENTAGE."%",
                        'net_price'            => $productPrice - (float)$price['segment_discount'] - ($productPrice) * (\App\Product::IVA_PERCENTAGE) / 100,
                        'add_duty'             => $product->getDuty( $ckey)."%",
                        'add_profit'           => number_format($price['promotion'],2,'.',''),
                        'add_profit_per'       => (float)$price['promotion_per'],
                        'final_price'          => number_format($price['total'],2,'.',''),
                        'country_code'         => $ckey,
                        'country_name'         => $cco,
                    ];
                }
            }
        }
        if ($request->ajax()) {
            $count = $request->count;
    		$view = view('product_price.index_ajax',compact('product_list', 'count'))->render();
            return response()->json(['html'=>$view, 'page'=>$request->page, 'count'=>$count]);
        }
        return view('product_price.index',compact('countryGroups','product_list', 'suppliers', 'websites', 'brands', 'selected_suppliers', 'selected_brands', 'selected_websites'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function update_product(Request $request){

        $product_array = json_decode($request->product_array);
        $response_array = [];
        foreach($product_array as $key => $p){
            if($request->route()->getName() == 'product.pricing.update.add_duty'){
                if($p->row_id == $request->row_id){
                    $duty = SimplyDutyCountry::where('country_code', $p->country_code)->first();
                    $duty->default_duty = str_replace('%', '', $request->add_duty);
                    $duty->save();
                    $add_duty = str_replace('%', '', $request->add_duty);
                }
            }else if($request->route()->getName() == 'product.pricing.update.add_profit'){
                if($p->row_id == $request->row_id){
                    $ref_product = Product::find($p->product_id);
                    $result = $ref_product->getPrice($p->storewebsitesid, $p->country_code,null, true,$p->add_duty, null, $request->add_profit, null, isset($ref_product->suppliers_info) ?  $ref_product->suppliers_info[0]->price : 0, $ref_product->category_segment);
                    if($result['status'] == false){
                        return response()->json(['status' => false, 'message' => $result['field'] ]);
                    }
                }
            }else {
                if($p->row_id == $request->row_id){
                    $ref_product = Product::find($p->product_id);
                    $ref_product->getPrice($p->storewebsitesid, $p->country_code,null, true,$p->add_duty, (int) str_replace('%', '', $request->seg_discount), null, null, isset($ref_product->suppliers_info) ?  $ref_product->suppliers_info[0]->price : 0, $ref_product->category_segment);
                }
            }
            
            $product = Product::find($p->product_id);
            $price = $product->getPrice( $p->storewebsitesid, $p->country_code,null, true, empty($add_duty) ? $p->add_duty : $add_duty, null, null, 'checked_add_profit', isset($product->suppliers_info) ?  $product->suppliers_info[0]->price : 0, $product->category_segment);
            $arr['status']               = $price['status'];
            $arr['row_id']               = $p->row_id;
            $arr['seg_discount']         = (float)$price['segment_discount'];
            $arr['segment_discount_per'] = (float)$price['segment_discount_per'];
            $arr['add_profit']           = number_format($price['promotion'],2,'.','');
            $arr['add_profit_per']       = number_format($price['promotion_per'],2,'.','');
            $arr['price']                = number_format($price['total'],2,'.','');
            $arr['add_duty']             = empty($add_duty) ? $p->add_duty : $add_duty . '%';
            $response_array[] = $arr;
        }
        return response()->json(['data' => $response_array, 'status' => true]);
    }

}
