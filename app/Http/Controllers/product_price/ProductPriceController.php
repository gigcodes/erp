<?php

namespace App\Http\Controllers\product_price;

use App\Http\Controllers\Controller;
use App\CountryGroup;
use App\Category;
use App\StoreWebsite;
use App\WebsiteStore;
use App\Product;
use App\Setting;
use App\Brand;
use App\Supplier;
use App\CategorySegmentDiscount;
use App\SimplyDutyCountryHistory;
use App\SimplyDutyCountry;
use App\SimplyDutySegment;
use App\Helpers\StatusHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Loggers\LogListMagento;
use App\Jobs\PushToMagento;

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
        
        $categoryIds = Category::pluck('id')->toArray(); 
        $categories = Category::whereNotIn('parent_id', $categoryIds)->where('parent_id', '>', 0)->select('id', 'title')->get()->toArray();
        
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
            
            $products = $products->skip($skip * Setting::get('pagination'))->limit('25')->get();
            //$products = $products->limit(100)->get();
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

        $category_segments = \App\CategorySegment::where('status',1)->get();

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
            $view = view('product_price.index_ajax',compact('product_list', 'count', 'category_segments'))->render();
            return response()->json(['html'=>$view, 'page'=>$request->page, 'count'=>$count]);
        }
      
        return view('product_price.index',compact('countryGroups','product_list', 'suppliers', 'websites', 'brands', 'selected_suppliers', 'selected_brands', 'selected_websites','category_segments','categories'));
    }



    public function store_website_product_prices(Request $request)
    {
        
        ini_set("memory_limit", -1);
    	$filter_data = $request->input();
        $skip = empty($request->page) ? 0 : $request->page;
        $products= \App\StoreWebsiteProductPrice::join('products','store_website_product_prices.product_id','products.id')
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
        ->leftJoin("product_suppliers as psu", function ($q) {
            $q->on("psu.product_id", "products.id");
        })
        ->leftJoin('store_websites','store_websites.id','store_website_product_prices.store_website_id')
       ->leftJoin('websites','store_website_product_prices.web_store_id','websites.id' )
       // ->leftJoin('websites','store_website_product_prices.store_website_id','websites.store_website_id' )
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
                
                store_websites.id as store_websites_id,
                store_websites.website as product_website,
                products.brand,
                store_website_product_prices.id as store_website_product_prices_id,
                store_website_product_prices.default_price,
                store_website_product_prices.segment_discount,
                store_website_product_prices.duty_price ,
                store_website_product_prices.override_price,
                store_website_product_prices.status,
                websites.name as countries'                
            )); 
			if (isset($filter_data['websites']) && is_array($filter_data['websites']) && $filter_data['websites'][0] != null) {
               $products = $products->whereIn('store_websites.id', $filter_data['websites']);
            }
			if (isset($filter_data['country_code'])) {
                $products = $products->where('websites.code', strtolower($filter_data['country_code'])); 
            }
			
            $products = $products->whereNull('products.deleted_at');
        
          /*  
            
            if (isset($filter_data['supplier']) && is_array($filter_data['supplier']) && $filter_data['supplier'][0] != null) {
                $suppliers_list = implode(',', $filter_data['supplier']);
                $products       = $products->whereRaw(\DB::raw("products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($suppliers_list))"));
            }
            
            if (isset($filter_data['brand_names']) && is_array($filter_data['brand_names']) && $filter_data['brand_names'][0] != null) {
                $products = $products->whereIn('brand_id', $filter_data['brand_names']);
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
            $products = $products->paginate(50);
            $product_list = [];
                foreach($products as $p){ 
            if(count($products)){
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
                        'override_price'         => $p->override_price,
                        'store_website_product_prices_id'=>$p->store_website_product_prices_id,
                        'status'=>$p->status,
                        'default_duty'=>$p->default_duty,
                        'countries'=>$p->countries

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
  $category_segments = \App\CategorySegment::where('status',1)->get();
        
        if ($request->ajax()) {
            $count = $request->count;
    		$view = view('product_price.index_ajax',compact('product_list', 'count','category_segments'))->render();
            return response()->json(['html'=>$view, 'page'=>$request->page, 'count'=>$count]);
        }
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
        DB::table('category_segment_discounts')->where('id', $request->seg_id)->update(['amount' => $request->seg_discount]);
        return response()->json(['data' => $response_array, 'status' => true]);
    }



    public function genericPricingAll($request) {

        $product_price = 100;
        $final_price = 100;
        $ids = $request->all();

        $categoryIds = Category::pluck('id')->toArray();
        $categories = Category::whereNotIn('parent_id', $categoryIds)
                        ->where('parent_id', '>', 0)
                        ->select('id', 'title')
                        ->orderBy('title','asc')
                        ->get()->toArray();

        //$cat_id = isset($ids['id']) ? $ids['id'] :$categories[0]['id'];
        
        $skip = empty($request->page) ? 0 : $request->page;

        ini_set('memory_limit', -1);
        $product_list = [];

        $countries = SimplyDutyCountry::select('*');

        if(isset($request->order) && isset($request->input)){
            if($request->input=='csegment'){
                $countries =$countries->orderBy('country_code',$request->order);
            }
        }

        $countries = $countries->get()->toArray();
        
        
        //$brands = Brand::select('id', 'name')->get()->toArray();
        $brands =\App\StoreWebsite::where('store_websites.is_published', 1)
            ->crossJoin('products')
            ->leftJoin('brands', 'brands.id', 'products.brand')
            ->Join('categories', 'categories.id', 'products.category')
            ->leftJoin("category_segments as cs", function ($q) {
                $q->on("categories.category_segment_id", "cs.id");
            })
            // ->join("category_segments as cs", function ($q) {
            //     $q->on("categories.category_segment_id", "cs.id");
            // })
            ->whereNotNull('brands.name')
            ->select('brands.id', 'brands.name','brands.brand_segment','products.category as catId','store_websites.id as store_websites_id','store_websites.website as product_website','categories.title as cate_title','cs.name as country_segment','products.id as pid');
                //->groupBy('products.brand')
                //->limit(50)->get()->toArray();
        //old query 09-09-2021
        // $brands = Product::leftJoin('brands', 'brands.id', 'products.brand')->whereNotNull('brands.name')
        //             ->select('brands.id', 'brands.name','brands.brand_segment','products.category as catId')
        //             ->groupBy('products.brand')->limit(50)->get()->toArray();
        $i = 0;

        if(isset($request->order) && isset($request->input)){
            if($request->input=='category'){
                $brands->orderBy('cate_title',$request->order);
            }

            if($request->input=='website'){
                $brands->orderBy('product_website',$request->order);
            }

            if($request->input=='bsegment'){
                $brands->orderBy('brands.brand_segment',$request->order);
            }

            if($request->input=='csegment'){
                $brands->orderBy('cs.name',$request->order);
            }
        }

        $brands = $brands->skip($skip * Setting::get('pagination'))
        ->limit('5')->get()->toArray();

        $countriesCount = count($countries);
        $category_segments = \App\CategorySegment::where('status',1)->get();
        
            foreach($brands as $brand) {
               $country = $countries[$i];
              //  foreach ($category_segments as $key => $value) {
                $category_segment_discount = \DB::table("categories")->join("category_segments as cs", "cs.id", "categories.category_segment_id")
                        ->join("category_segment_discounts as csd", "csd.category_segment_id", "cs.id")
                        ->where('categories.id',$brand['catId'])
                        ->where('csd.brand_id', $brand['id'])
                        ->select("csd.*",'cs.name as category_segment')
                        ->first();                   

                   //$category_segment_discount = DB::table('category_segment_discounts')->where('category_id', $cat_id)->where('brand_id', $brand['id'])->first();
                    if($category_segment_discount != null) {
                        if($category_segment_discount->amount!='' && $category_segment_discount->amount_type == 'percentage'){
                            
                            if($category_segment_discount->amount!='' || $category_segment_discount->amount !=0 ){
                                //$final_price = ($product_price * $category_segment_discount->amount)/100;
                                $catDisc = ($product_price * $category_segment_discount->amount)/100;
                                $final_price = $final_price - $catDisc;
                            }

                        }elseif($category_segment_discount->amount_type == 'amount'){
                            if($category_segment_discount->amount!='' || $category_segment_discount->amount !=0 ){
                                $final_price = $final_price-$category_segment_discount->amount;
                            }
                        }
                    }
                //}
                if(\App\Product::IVA_PERCENTAGE!=0){
                    $IVA = \App\Product::IVA_PERCENTAGE;
                    $lessIva = ( $final_price * $IVA )/100;
                    $final_price = $final_price - $lessIva;
                }

                if($country['default_duty']!='' || $country['default_duty']!=0){
                    $dutyDisc = ($final_price * $country['default_duty'])/100;
                    $final_price = $final_price + $dutyDisc;
                }

                $product = Product::find($brand['pid']);
                $dutyPrice = $product->getDuty($country['country_code']);
                $category_segment = isset($brand['country_segment'])  ? $brand['country_segment'] : $brand['brand_segment'];
                $price = $product->getPrice($brand['store_websites_id'],$country['country_code'],null, true,$dutyPrice, null, null, null, isset($product->suppliers_info[0]) ?  $product->suppliers_info[0]->price : 0, $category_segment);

                // if($country['segment_id']!='' || $country['segment_id']!=0){
                //    $dutysegment = SimplyDutySegment::where('id',$country['segment_id'])->first();
                //    $country['dutySegment'] = $dutysegment->segment;
                // }else{
                //     $country['dutySegment'] = '';
                // }
                
                $country['dutySegment']= isset($brand['country_segment'])  ? $brand['country_segment'] : $brand['brand_segment'];//$country['country_code'];

                $categoryDetail = Category::where('id',$brand['catId'])->select('id', 'title')->first();
                
                $product_list[] = [
                    'catId'=>$categoryDetail ? $categoryDetail->id :'', 
                    'categoryName'=> $categoryDetail ? $categoryDetail->title :'', 
                    'product'=>'Product For Brand', 
                    'brandId'=>$brand['id'], 
                    'brandName'=>$brand['name'], 
                    'brandSegment'=>$brand['brand_segment'],
                    'store_websites_id'=>$brand['store_websites_id'],
                    'product_website'=>$brand['product_website'],
                    'country'=>$country,
                    'product_price'=>100,
                    'add_profit' => number_format($price['promotion'],2,'.',''),
                    'add_profit_per'=> (float)$price['promotion_per'],
                    'less_IVA'=>\App\Product::IVA_PERCENTAGE."%",
                    'final_price'=>$final_price,
                    'cate_segment_discount'=>$category_segment_discount->amount,
                    'cate_segment_discount_type'=>$category_segment_discount->amount_type,
                ];

                if($i< $countriesCount-1) {
                    $i++;
                } else {
                    $i = 0;
                }

                $product_price = 100;
                $final_price = 100;

            }
        return array('product_list'=>$product_list,'category_segments'=>$category_segments,'categories'=>$categories);
    }
	
	public function genericPricing(Request $request) {

        $product_price = 100;
        $final_price = 100;
        $ids = $request->all();

        if(!isset($ids['id'])){
            $data = $this->genericPricingAll($request);
            $product_list =$data['product_list'];
            $category_segments =$data['category_segments'];
            $categories =$data['categories'];

            if ($request->ajax()) {
                $count = $request->count;

                $view =view('product_price.generic_price_ajax', compact('product_list', 'category_segments'))->render();

                return response()->json(['html'=>$view, 'page'=>$request->page, 'count'=>$count]);
            }
            return view('product_price.generic_price', compact('product_list', 'category_segments','categories'));
        }

        $categoryIds = Category::pluck('id')->toArray(); 
        $categories = Category::whereNotIn('parent_id', $categoryIds)->where('parent_id', '>', 0)->select('id', 'title')->orderBy('title','asc')->get()->toArray();

        $cat_id = isset($ids['id']) ? $ids['id'] :$categories[0]['id'];

		ini_set('memory_limit', -1);
		$product_list = [];

		$countries = SimplyDutyCountry::select('*');

        if(isset($request->order) && isset($request->input)){
            if($request->input=='csegment'){
                $countries =$countries->orderBy('country_code',$request->order);
            }
        }

        $countries = $countries->get()->toArray();

		$categoryDetail = Category::where('id',$cat_id)->select('id', 'title')->first();
        
		//$brands = Brand::select('id', 'name')->get()->toArray();
        $skip = empty($request->page) ? 0 : $request->page;

        $brands =\App\StoreWebsite::where('store_websites.is_published', 1)
            ->crossJoin('products')
            ->leftJoin('brands', 'brands.id', 'products.brand')
            ->Join('categories', 'categories.id', 'products.category')
            ->leftJoin("category_segments as cs", function ($q) {
                $q->on("categories.category_segment_id", "cs.id");
            })
            ->whereNotNull('brands.name')
            ->where('products.category', $cat_id)
            ->select('brands.id', 'brands.name','brands.brand_segment','products.category as catId','store_websites.id as store_websites_id',
                'store_websites.website as product_website','categories.title as cate_title','cs.name as country_segment');
            //->groupBy('products.brand')
           // ->limit(50)->get()->toArray();
            //old query 09-09-2021
		// $brands = Product::leftJoin('brands', 'brands.id', 'products.brand')
		// 		  ->where('category', $cat_id)->whereNotNull('brands.name')->select('brands.id', 'brands.name','brands.brand_segment')->groupBy('products.brand')->get()->toArray();


        if(isset($request->order) && isset($request->input)){
            if($request->input=='category'){
                $brands->orderBy('cate_title',$request->order);
            }

            if($request->input=='website'){
                $brands->orderBy('product_website',$request->order);
            }

            if($request->input=='bsegment'){
                $brands->orderBy('brands.brand_segment',$request->order);
            }

            if($request->input=='csegment'){
                $brands->orderBy('cs.name',$request->order);
            }
        }


        $brands = $brands->skip($skip * Setting::get('pagination'))
        ->limit('5')->get()->toArray();
		$i = 0;

		$countriesCount = count($countries);
        $category_segments = \App\CategorySegment::where('status',1)->get();
        
			foreach($brands as $brand) {
               $country = $countries[$i];

              //  foreach ($category_segments as $key => $value) {
				$category_segment_discount = \DB::table("categories")->join("category_segments as cs", "cs.id", "categories.category_segment_id")
                        ->join("category_segment_discounts as csd", "csd.category_segment_id", "cs.id")
                        ->where('categories.id',$brand['catId'])
                        ->where('csd.brand_id', $brand['id'])
                        ->select("csd.*",'cs.name as category_segment')
                        ->first();                   

				   //$category_segment_discount = DB::table('category_segment_discounts')->where('category_id', $cat_id)->where('brand_id', $brand['id'])->first();
                    if($category_segment_discount != null) {
						if($category_segment_discount->amount!='' && $category_segment_discount->amount_type == 'percentage'){
							
							if($category_segment_discount->amount!='' || $category_segment_discount->amount !=0 ){
								//$final_price = ($product_price * $category_segment_discount->amount)/100;
								$catDisc = ($product_price * $category_segment_discount->amount)/100;
								$final_price = $final_price - $catDisc;
							}

						}elseif($category_segment_discount->amount_type == 'amount'){
							if($category_segment_discount->amount!='' || $category_segment_discount->amount !=0 ){
								$final_price = $final_price-$category_segment_discount->amount;
							}
						}
                    }
                //}
				if(\App\Product::IVA_PERCENTAGE!=0){
                    $IVA = \App\Product::IVA_PERCENTAGE;
                    $lessIva = ( $final_price * $IVA )/100;
					$final_price = $final_price - $lessIva;
                }

                if($country['default_duty']!='' || $country['default_duty']!=0){
                    $dutyDisc = ($final_price * $country['default_duty'])/100;
					$final_price = $final_price + $dutyDisc;
                }

                // if($country['segment_id']!='' || $country['segment_id']!=0){
                //    $dutysegment = SimplyDutySegment::where('id',$country['segment_id'])->first();
                //    $country['dutySegment'] = $dutysegment->segment;
                // }else{
                //     $country['dutySegment'] = '';
                // }

                $country['dutySegment']= isset($brand['country_segment'])  ? $brand['country_segment'] : $brand['brand_segment'];

                //$country['dutySegment']= $country['country_code'];
               
                
				$product_list[] = [
                    'catId'=>$categoryDetail->id, 
                    'categoryName'=>$categoryDetail->title, 
                    'product'=>'Product For Brand', 
					'brandId'=>$brand['id'], 
                    'brandName'=>$brand['name'], 
                    'brandSegment'=>$brand['brand_segment'],
                    'store_websites_id'=>$brand['store_websites_id'],
                    'product_website'=>$brand['product_website'],
                    'country'=>$country,
                    'product_price'=>100,
                    'less_IVA'=>\App\Product::IVA_PERCENTAGE."%",
                    'final_price'=>$final_price,
                    'cate_segment_discount'=>$category_segment_discount->amount,
                    'cate_segment_discount_type'=>$category_segment_discount->amount_type,
				];

				if($i< $countriesCount-1) {
					$i++;
				} else {
					$i = 0;
				}

                $product_price = 100;
                $final_price = 100;

			}

        if ($request->ajax()) {
            $count = $request->count;
            $view =view('product_price.generic_price_ajax', compact('product_list', 'category_segments'))->render();
            return response()->json(['html'=>$view, 'page'=>$request->page, 'count'=>$count]);
        }
		
		return view('product_price.generic_price', compact('product_list', 'category_segments','categories'));
	}

    public function updateProduct(Request $request) { 
        //echo"<pre>";print_r($request->all());die();

        if(isset($request->segmentId1)){
            $catSegDiscount = CategorySegmentDiscount::where(['category_segment_id'=>$request->segmentId1,'brand_id'=>$request->brandId])->first();
            if($catSegDiscount == null) {
                CategorySegmentDiscount::create(['category_segment_id'=>$request->segmentId1, 'amount'=>$request->segmentprice1, 'brand_id'=>$request->brandId]);
            } else {
                $catSegDiscount->update([ 'amount'=>$request->segmentprice1]);
            }
        }

        if(isset($request->segmentId2)){
            $catSegDiscount = CategorySegmentDiscount::where(['category_segment_id'=>$request->segmentId2,'brand_id'=>$request->brandId])->first();
            if($catSegDiscount == null) {
                CategorySegmentDiscount::create(['category_segment_id'=>$request->segmentId2, 'amount'=>$request->segmentprice2, 'brand_id'=>$request->brandId]);
            } else {
                $catSegDiscount->update([ 'amount'=>$request->segmentprice2]);
            }
        }
		 if(isset($request->default_duty)){
            $duty = SimplyDutyCountry::find($request->countryId);
            $duty->default_duty = $request->default_duty;

            $data=[
                'simply_duty_countries_id'=>$duty->id,
                'old_segment'=>$duty->segment_id, 
                'new_segment'=>$duty->segment_id,
                'old_duty'=>$duty->default_duty,
                'new_duty'=>$request->input('duty'),
                'updated_by'=>Auth::user()->id
    
            ];
            //$duty->default_duty =$request->default_duty;
            $duty->status=0;
            SimplyDutyCountryHistory::insert($data);
            if ($duty->save()) {
				$amount = $request->input('duty'); $code = $duty->country_code;
				$ps= \App\StoreWebsiteProductPrice::select('store_website_product_prices.id','store_website_product_prices.duty_price',
				   'store_website_product_prices.product_id','store_website_product_prices.store_website_id','websites.code')
				   ->leftJoin('websites','store_website_product_prices.web_store_id','websites.id' )
				   ->where('websites.code',strtolower($code))
				   ->get(); //dd($ps);
				    if ($ps)
				    {
						foreach($ps as $p)
						{ 
						  \App\StoreWebsiteProductPrice::where('id',$p->id)->update(['duty_price'=>$amount,'status'=>0]) ;
						   $note="Country Duty changed  from ".$p->duty_price." To ".$amount;
						   \App\StoreWebsiteProductPriceHistory::insert(['sw_product_prices_id'=>$p->id,'updated_by'=>Auth::id(),'notes'=>$note]);
					   }
					}
            }
			
			$ps= \App\Product::select('products.id','store_website_product_prices.store_website_id')
			->leftJoin('store_website_product_prices', 'store_website_product_prices.product_id', '=', 'products.id')
				   ->where('store_website_id',$request->input('websiteId'))
				   ->where('brand',$request->input('brandId') )
				   ->where('category',$request->input('catId'))->get();
			foreach($ps as $p)
			{ 
			 $this->pushToMagento($p->product_id, $p->store_website_id);
			}
						  
        }
        return json_encode(['status'=>true]);
    }

	
	public function updateProductPrice(Request $request) { 
		if($request->route()->getName() == 'updateDutyPrice'){ 
		   $duty = SimplyDutyCountry::find($request->countryId);
           $duty->default_duty = $request->dutyPrice;
           $duty->save();
		} else if($request->route()->getName() == 'updateSegmentPrice') { 
			$catSegDiscount = CategorySegmentDiscount::where(['category_segment_id'=>$request->segmentId,'brand_id'=>$request->brandId])->first();
			if($catSegDiscount == null) {
				CategorySegmentDiscount::create(['category_segment_id'=>$request->segmentId, 'amount'=>$request->price, 'brand_id'=>$request->brandId]);
			} else {
		     	$catSegDiscount->update([ 'amount'=>$request->price]);
			}
		}
		return json_encode(['status'=>true]);
	}

    public function approve(Request $request)
    {
         $ids=$request->ids;
         $ids=explode(",", $ids);
         for($i=0;$i<count($ids);$i++)
         {
             if ($ids[$i]>0)
             {
             \App\StoreWebsiteProductPrice::where('id',$ids[$i])->update(['status'=> 1]);
            
             }
                
         }
         return response()->json(["code" => 200 , "message" => "Approved Successfully"]);
    }

    public function storewebsiteproductpriceshistory(Request $request)
    {
          $id=$request->id;
          $history=\App\StoreWebsiteProductPriceHistory::where('sw_product_prices_id',$id)->orderBy('created_at','desc')->get();
          $html="<table class='table table-bordered table-striped'> <thead><tr><th>Date</th><th>Notes</th></thead> <tbody>";
          foreach ($history as $h)
          {
            $html.="<tr><td>".$h->created_at."</td>";  
            $html.="<td>".$h->notes."</td></tr>";

          }
          $html.=" </tbody> </table>";

          echo $html;

    }

    public function update_store_website_product_prices($code,$amount)
   {
           $ps= \App\StoreWebsiteProductPrice::select('store_website_product_prices.id','store_website_product_prices.duty_price',
           'store_website_product_prices.product_id','store_website_product_prices.store_website_id','websites.code')
           ->leftJoin('websites','store_website_product_prices.web_store_id','websites.id' )
           ->where('websites.code',strtolower($code))
           ->get(); //dd($ps);
           if ($ps)
           {
            foreach($ps as $p)
            { 
              \App\StoreWebsiteProductPrice::where('id',$p->id)->update(['duty_price'=>$amount,'status'=>0]) ;
               $note="Country Duty changed  from ".$p->duty_price." To ".$amount;
               $this->pushToMagento($p->product_id, $p->store_website_id);
               \App\StoreWebsiteProductPriceHistory::insert(['sw_product_prices_id'=>$p->id,'updated_by'=>Auth::id(),'notes'=>$note]);
           }
        }
   } 

   public function update_store_website_product_segment($code, $segmentDiscount)
   {
           $ps= \App\StoreWebsiteProductPrice::select('store_website_product_prices.id','store_website_product_prices.duty_price','websites.code')
           ->leftJoin('websites','store_website_product_prices.web_store_id','websites.id' )
           ->where('websites.code',strtolower($code))
           ->get(); //dd($ps);
           if ($ps)
           {
            foreach($ps as $p)
            { 
              \App\StoreWebsiteProductPrice::where('id',$p->id)->update(['segment_discount'=>$segmentDiscount,'status'=>0]) ;
               //$note="Country Duty change  from ".$p->duty_price." To ".$amount;
               //\App\StoreWebsiteProductPriceHistory::insert(['sw_product_prices_id'=>$p->id,'updated_by'=>Auth::id(),'notes'=>$note]);
           }
        }
   }

    public function pushToMagento($productId, $websiteId) {
        $product = \App\Product::find($productId);
      
       if ($product) {
            $website = StoreWebsite::where('id', $websiteId)->first();
            if ($website == null) {
                \Log::channel('productUpdates')->info("Product started " . $product->id . " No website found");
                $msg = 'No website found for  Brand: ' . $product->brand . ' and Category: ' . $product->category;
                //ProductPushErrorLog::log($product->id, $msg, 'error');
                //LogListMagento::log($product->id, "Start push to magento for product id " . $product->id, 'info');
                echo $msg;die;
            } else {
                $i = 1;
                
                    if ($website) {
                        // testing 
                        \Log::channel('productUpdates')->info("Product started website found For website" . $website->website);
                        $log = LogListMagento::log($product->id, "Start push to magento for product id " . $product->id, 'info', $website->id);
                        //currently we have 3 queues assigned for this task.
                        if ($i > 3) {
                            $i = 1;
                        }
                        $log->queue = \App\Helpers::createQueueName($website->title);
                        $log->save();
                        PushToMagento::dispatch($product,$website , $log)->onQueue($log->queue);
                        //PushToMagento::dispatch($product, $website, $log)->onQueue($queueName[$i]);
                        $i++;
                    }
                
            }
        }
   }

}
