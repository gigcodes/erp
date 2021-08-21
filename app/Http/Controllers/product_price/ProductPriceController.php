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
use Illuminate\Support\Facades\DB;

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
    	$filter_data = $request->input();
        $skip = empty($request->page) ? 0 : $request->page;
        $products = \App\StoreWebsite::where('store_websites.is_published', 1)
            ->crossJoin('products')
            ->crossJoin('simply_duty_countries')
            ->leftJoin("brands as b", function ($q) {
                $q->on("b.id", "products.brand");
            })
            ->leftJoin("categories as c", function ($q) {
                $q->on("c.id", "products.category");
            })
            ->leftJoin("category_segments as cs", function ($q) {
                $q->on("c.category_segment_id", "cs.id");
            })
            ->leftJoin("scraped_products as sp", function ($q) {
                $q->on("sp.product_id", "products.id");
            })
            ->Join("product_suppliers as psu", function ($q) {
                $q->on("psu.product_id", "products.id");
            })
            ->select(DB::raw('
                products.id as pid, 
                products.name as product_name,
                b.name as brand_name,
                b.id as brand_id,
                cs.name as category_segment,
                b.brand_segment as brand_segment,
                c.title as category_name,
                products.category,
                products.supplier,
                products.sku,
                products.size,
                products.color,
                products.suggested_color,
                products.composition,
                products.size_eu,
                products.stock,
                psu.size_system,
                status_id,
                sub_status_id,
                products.created_at,
                products.id as pid,
                simply_duty_countries.country_code as product_country_code,
                simply_duty_countries.country_name as product_country_name,
                store_websites.id as store_websites_id,
                store_websites.website as product_website,
                products.brand'
            ));
            $products = $products->whereNull('products.deleted_at');
        
            if (isset($filter_data['country_code'])) {
                $products = $products->where('simply_duty_countries.country_code', $filter_data['country_code']); 
            }
            
            if (isset($filter_data['supplier']) && is_array($filter_data['supplier']) && $filter_data['supplier'][0] != null) {
                $suppliers_list = implode(',', $filter_data['supplier']);
                $products       = $products->whereRaw(\DB::raw("products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($suppliers_list))"));
            }
            
            if (isset($filter_data['brand_names']) && is_array($filter_data['brand_names']) && $filter_data['brand_names'][0] != null) {
                $products = $products->whereIn('brand_id', $filter_data['brand_names']);
            }
            
            if (isset($filter_data['websites']) && is_array($filter_data['websites']) && $filter_data['websites'][0] != null) {
                $products = $products->whereIn('store_websites.id', $filter_data['websites']);
            }

            if (isset($filter_data['term'])) {
                $term  = $filter_data['term'];
                $products = $products->where(function ($q) use ($term) {
                    $q->where('products.name', 'LIKE', "%$term%")
                        ->orWhere('products.sku', 'LIKE', "%$term%")
                        ->orWhere('c.title', 'LIKE', "%$term%")
                        ->orWhere('b.name', 'LIKE', "%$term%")
                        ->orWhere('products.id', 'LIKE', "%$term%");
                });
            }
            
            // $products = $products->orderby('products.id', 'desc'); // FOR LATEST PRODUCT
            
         /*   $products = $products->skip($skip * Setting::get('pagination'))
            ->limit(Setting::get('pagination'))->get();*/
            $products = $products->get();
            $product_list = [];
            if(count($products)){
                foreach($products as $p){
                    $product = Product::find($p->pid);
                    $dutyPrice = $product->getDuty( $p->product_country_code );
                    $category_segment = $p->category_segment != null ? $p->category_segment : $p->brand_segment;
                    $price = $product->getPrice( $p->store_websites_id, $p->product_country_code,null, true,$dutyPrice, null, null, null, isset($product->suppliers_info) ?  $product->suppliers_info[0]->price : 0, $category_segment);
                    $ivaPercentage = \App\Product::IVA_PERCENTAGE;
                    $productPrice = number_format($price['original_price'],2,'.','');
                    $product_list[] = [
                        'storeWebsitesID'      => $p->store_websites_id,
                        'getPrice'             => $price,
                        'id'                   => $product->id,
                        'sku'                  => $product->sku, 
                        'brand'                => isset($p->brand_name) ? $p->brand_name : "-" , 
                        'brand_id'              => isset($p->brand) ? $p->brand : "0" , 
                        'segment'              => $category_segment,
                        'website'              => $p->product_website,
                        'eur_price'            => $productPrice,
                        'seg_discount'         => (float)$price['segment_discount'],
                        'segment_discount_per' => (float)$price['segment_discount_per'],
                        'iva'                  => \App\Product::IVA_PERCENTAGE."%",
                        'net_price'            => $productPrice - (float)$price['segment_discount'] - ($productPrice) * (\App\Product::IVA_PERCENTAGE) / 100,
                        'add_duty'             => $product->getDuty( $p->product_country_code)."%",
                        'add_profit'           => number_format($price['promotion'],2,'.',''),
                        'add_profit_per'       => (float)$price['promotion_per'],
                        'final_price'          => number_format($price['total'],2,'.',''),
                        'country_code'         => $p->product_country_code,
                        'country_name'         => $p->product_country_name,
                    ];
                }
            }
        $countryGroups = SimplyDutyCountry::getSelectList();
        $cCodes = $countryGroups;
        if(!empty($request->country_code)) {
            $cCodes = SimplyDutyCountry::where("country_code",$request->country_code)->pluck("country_name","country_code")->toArray();//$request->country_code;
        }

        $suppliers = [];
        $brands = [];
        $websites = StoreWebsite::where("is_published","1")->get()->pluck('title', 'id')->toArray();
        $storeWebsites = StoreWebsite::select('title', 'id','website')->where("is_published","1");
        if($request->websites && !empty($request->websites)){
            $storeWebsites = $storeWebsites->whereIn('id', $request->websites);
        } 
        $storeWebsites = $storeWebsites->get()->toArray(); 
    	  
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
  
        if ($request->ajax()) {
            $count = $request->count;
    		$view = view('product_price.index_ajax',compact('product_list', 'count'))->render();
            return response()->json(['html'=>$view, 'page'=>$request->page, 'count'=>$count]);
        }
        $category_segments = \App\CategorySegment::where('status',1)->get();
        return view('product_price.index',compact('countryGroups','product_list', 'suppliers', 'websites', 'brands', 'selected_suppliers', 'selected_brands', 'selected_websites','category_segments'));
    }



    public function store_website_product_prices(Request $request)
    {
        
        ini_set("memory_limit", -1);
    	$filter_data = $request->input();
        $skip = empty($request->page) ? 0 : $request->page;
        $products = \App\StoreWebsite::where('store_websites.is_published', 1)
            ->crossJoin('products')
            ->crossJoin('simply_duty_countries')
            ->join('store_website_product_prices','store_website_product_prices.product_id','products.id')
            ->leftJoin("brands as b", function ($q) {
                $q->on("b.id", "products.brand");
            })
            ->leftJoin("categories as c", function ($q) {
                $q->on("c.id", "products.category");
            })
            ->leftJoin("category_segments as cs", function ($q) {
                $q->on("c.category_segment_id", "cs.id");
            })
            ->leftJoin("scraped_products as sp", function ($q) {
                $q->on("sp.product_id", "products.id");
            })
            ->Join("product_suppliers as psu", function ($q) {
                $q->on("psu.product_id", "products.id");
            })
            ->select(DB::raw('
                products.id as pid, 
                products.name as product_name,
                b.name as brand_name,
                b.id as brand_id,
                cs.name as category_segment,
                b.brand_segment as brand_segment,
                c.title as category_name,
                products.category,
                products.supplier,
                products.sku,
                products.size,
                products.color,
                products.suggested_color,
                products.composition,
                products.size_eu,
                products.stock,
                psu.size_system,
                status_id,
                sub_status_id,
                products.created_at,
                products.id as pid,
                simply_duty_countries.country_code as product_country_code,
                simply_duty_countries.country_name as product_country_name,
                store_websites.id as store_websites_id,
                store_websites.website as product_website,
                products.brand,
                store_website_product_prices.default_price,
                store_website_product_prices.segment_discount,
                store_website_product_prices.duty_price ,
                store_website_product_prices.override_price '
                
            ));
            $products = $products->whereNull('products.deleted_at');
        
            if (isset($filter_data['country_code'])) {
                $products = $products->where('simply_duty_countries.country_code', $filter_data['country_code']); 
            }
            
            if (isset($filter_data['supplier']) && is_array($filter_data['supplier']) && $filter_data['supplier'][0] != null) {
                $suppliers_list = implode(',', $filter_data['supplier']);
                $products       = $products->whereRaw(\DB::raw("products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($suppliers_list))"));
            }
            
            if (isset($filter_data['brand_names']) && is_array($filter_data['brand_names']) && $filter_data['brand_names'][0] != null) {
                $products = $products->whereIn('brand_id', $filter_data['brand_names']);
            }
            
            if (isset($filter_data['websites']) && is_array($filter_data['websites']) && $filter_data['websites'][0] != null) {
                $products = $products->whereIn('store_websites.id', $filter_data['websites']);
            }

            if (isset($filter_data['term'])) {
                $term  = $filter_data['term'];
                $products = $products->where(function ($q) use ($term) {
                    $q->where('products.name', 'LIKE', "%$term%")
                        ->orWhere('products.sku', 'LIKE', "%$term%")
                        ->orWhere('c.title', 'LIKE', "%$term%")
                        ->orWhere('b.name', 'LIKE', "%$term%")
                        ->orWhere('products.id', 'LIKE', "%$term%");
                });
            }
            
            // $products = $products->orderby('products.id', 'desc'); // FOR LATEST PRODUCT
            
         /*   $products = $products->skip($skip * Setting::get('pagination'))
            ->limit(Setting::get('pagination'))->get();*/
            $products = $products->get();
            $product_list = [];
            if(count($products)){
                foreach($products as $p){
                    $product = Product::find($p->pid);
                    $dutyPrice = $product->getDuty( $p->product_country_code );
                    $category_segment = $p->category_segment != null ? $p->category_segment : $p->brand_segment;
                    $price = $product->getPrice( $p->store_websites_id, $p->product_country_code,null, true,$dutyPrice, null, null, null, isset($product->suppliers_info) ?  $product->suppliers_info[0]->price : 0, $category_segment);
                    $ivaPercentage = \App\Product::IVA_PERCENTAGE;
                    $productPrice = number_format($price['original_price'],2,'.','');
                    $product_list[] = [
                        'storeWebsitesID'      => $p->store_websites_id,
                        'getPrice'             => $price,
                        'id'                   => $product->id,
                        'sku'                  => $product->sku, 
                        'brand'                => isset($p->brand_name) ? $p->brand_name : "-" , 
                        'brand_id'              => isset($p->brand) ? $p->brand : "0" , 
                        'segment'              => $category_segment,
                        'website'              => $p->product_website,
                        'eur_price'            => $productPrice,
                        'seg_discount'         => (float)$price['segment_discount'],
                        'segment_discount_per' => (float)$price['segment_discount_per'],
                        'iva'                  => \App\Product::IVA_PERCENTAGE."%",
                        'net_price'            => $productPrice - (float)$price['segment_discount'] - ($productPrice) * (\App\Product::IVA_PERCENTAGE) / 100,
                        'add_duty'             => $product->getDuty( $p->product_country_code)."%",
                        'add_profit'           => number_format($price['promotion'],2,'.',''),
                        'add_profit_per'       => (float)$price['promotion_per'],
                        'final_price'          => number_format($price['total'],2,'.',''),
                        'country_code'         => $p->product_country_code,
                        'country_name'         => $p->product_country_name,
                        'default_price'         => $p->default_price,
                        'segment_discount'         => $p->segment_discount,
                        'duty_price'         => $p->duty_price,
                        'override_price'         => $p->override_price

                    ];
                }
            }
        $countryGroups = SimplyDutyCountry::getSelectList();
        $cCodes = $countryGroups;
        if(!empty($request->country_code)) {
            $cCodes = SimplyDutyCountry::where("country_code",$request->country_code)->pluck("country_name","country_code")->toArray();//$request->country_code;
        }

        $suppliers = [];
        $brands = [];
        $websites = StoreWebsite::where("is_published","1")->get()->pluck('title', 'id')->toArray();
        $storeWebsites = StoreWebsite::select('title', 'id','website')->where("is_published","1");
        if($request->websites && !empty($request->websites)){
            $storeWebsites = $storeWebsites->whereIn('id', $request->websites);
        } 
        $storeWebsites = $storeWebsites->get()->toArray(); 
    	  
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
  
        if ($request->ajax()) {
            $count = $request->count;
    		$view = view('product_price.index_ajax',compact('product_list', 'count'))->render();
            return response()->json(['html'=>$view, 'page'=>$request->page, 'count'=>$count]);
        }
        $category_segments = \App\CategorySegment::where('status',1)->get();
        return view('product_price.store-website-product-prices',compact('countryGroups','product_list', 'suppliers', 'websites', 'brands', 'selected_suppliers', 'selected_brands', 'selected_websites','category_segments'));
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
                    $category_segment = @$ref_product->categories->categorySegmentId->name == null ? (@$ref_product->brands->brand_segment != null ? $ref_product->brands->brand_segment : null) : $ref_product->categories->categorySegmentId->name;
                    $result = $ref_product->getPrice($p->storewebsitesid, $p->country_code,null, true,$p->add_duty, null, $request->add_profit, null, isset($ref_product->suppliers_info) ?  $ref_product->suppliers_info[0]->price : 0, $category_segment);
                    if($result['status'] == false){
                        return response()->json(['status' => false, 'message' => $result['field'] ]);
                    }
                }
            }else {
                if($p->row_id == $request->row_id){
                    $ref_product = Product::find($p->product_id);
                    $category_segment = @$ref_product->categories->categorySegmentId->name == null ? (@$ref_product->brands->brand_segment != null ? $ref_product->brands->brand_segment : null) : $ref_product->categories->categorySegmentId->name;
                    $ref_product->getPrice($p->storewebsitesid, $p->country_code,null, true,$p->add_duty, (int) str_replace('%', '', $request->seg_discount), null, null, isset($ref_product->suppliers_info) ?  $ref_product->suppliers_info[0]->price : 0, $category_segment);
                }
            }
            
            $product = Product::find($p->product_id);
            $category_segment = @$product->categories->categorySegmentId->name == null ? (@$product->brands->brand_segment != null ? $product->brands->brand_segment : null) : $product->categories->categorySegmentId->name;
            $price = $product->getPrice( $p->storewebsitesid, $p->country_code,null, true, empty($add_duty) ? $p->add_duty : $add_duty, null, null, 'checked_add_profit', isset($product->suppliers_info) ?  $product->suppliers_info[0]->price : 0, $category_segment);
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
