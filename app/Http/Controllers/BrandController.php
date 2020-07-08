<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Setting;
use App\Product;
use Illuminate\Http\Request;
use \App\StoreWebsiteBrand;

class BrandController extends Controller
{
    //

    public function __construct()
    {
      //  $this->middleware('permission:brand-edit', ['only' => 'index', 'create', 'store', 'destroy', 'update', 'edit']);
    }

    public function index()
    {

        $brands = Brand::leftJoin("store_website_brands as swb","swb.brand_id","brands.id")
        ->leftJoin("store_websites as sw","sw.id","swb.store_website_id")
        ->select(["brands.*",\DB::raw("group_concat(sw.id) as selling_on")])
        ->groupBy("brands.id")
        ->oldest()->whereNull('brands.deleted_at');

        $keyword = request('keyword');
        if(!empty($keyword)) {
            $brands = $brands->where("name","like","%".$keyword."%");
        }

        $brands = $brands->paginate(Setting::get('pagination'));

        $storeWebsite = \App\StoreWebsite::all()->pluck("website","id")->toArray();

        $attachedBrands = \App\StoreWebsiteBrand::groupBy("store_website_id")->select(
            [\DB::raw("count(brand_id) as total_brand"),"store_website_id"]
        )->get()->toArray();


        return view('brand.index', compact('brands','storeWebsite','attachedBrands'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function create()
    {

        $data[ 'name' ] = '';
        $data[ 'euro_to_inr' ] = '';
        $data[ 'deduction_percentage' ] = '';
        $data[ 'magento_id' ] = '';
        $data[ 'brand_segment' ] = '';
        $data[ 'brand_segment' ] = '';
        $data[ 'brand_segment' ] = '';

        $data[ 'modify' ] = 0;

        return view('brand.form', $data);
    }


    public function edit(Brand $brand)
    {

        $data = $brand->toArray();
        $data[ 'modify' ] = 1;

        return view('brand.form', $data);
    }


    public function store(Request $request, Brand $brand)
    {

        $this->validate($request, [
            'name' => 'required',
            'euro_to_inr' => 'required|numeric',
            'deduction_percentage' => 'required|numeric',
            'magento_id' => 'required|numeric',
        ]);

        $data = $request->except('_token', '_method');

        $brand->create($data);

        return redirect()->route('brand.index')->with('success', 'Brand added successfully');
    }

    public function update(Request $request, Brand $brand)
    {

        $this->validate($request, [
            'name' => 'required',
            'euro_to_inr' => 'required|numeric',
            'deduction_percentage' => 'required|numeric',
            'magento_id' => 'required|numeric',
        ]);

        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            $brand->$key = $value;
        }

        $brand->update();

        $products = Product::where('brand', $brand->id)->get();

        if (count($products) > 0) {
            foreach ($products as $product) {
                if (!empty($brand->euro_to_inr)) {
                    $product->price_inr = $brand->euro_to_inr * $product->price;
                } else {
                    $product->price_inr = Setting::get('euro_to_inr') * $product->price;
                }

                $product->price_inr = round($product->price_inr, -3);
                $product->price_inr_special = $product->price_inr - ($product->price_inr * $brand->deduction_percentage) / 100;

                $product->price_inr_special = round($product->price_inr_special, -3);

                $product->save();
            }
        }

        // $uploaded_products = Product::where('brand', $brand->id)->where('isUploaded', 1)->get();
        $uploaded_products = [];

        if (count($uploaded_products) > 0) {
            foreach ($uploaded_products as $product) {
                $this->magentoSoapUpdatePrices($product);
            }
        }

        return redirect()->route('brand.index')->with('success', 'Brand updated successfully');
    }

    public function destroy(Brand $brand)
    {
        $brand->scrapedProducts()->delete();
        $brand->products()->delete();
        $brand->delete();
        return redirect()->route('brand.index')->with('success', 'Brand Deleted successfully');

    }

    public static function getBrandName($id)
    {

        $brand = new Brand();
        $brand_instance = $brand->find($id);

        return $brand_instance ? $brand_instance->name : '';
    }

    public static function getBrandIds($term)
    {

        $brand = Brand::where('name', '=', $term)->first();

        return $brand ? $brand->id : 0;
    }

    public static function getEuroToInr($id)
    {

        $brand = new Brand();
        $brand_instance = $brand->find($id);

        return $brand_instance ? $brand_instance->euro_to_inr : 0;
    }

    public static function getDeductionPercentage($id)
    {

        $brand = new Brand();
        $brand_instance = $brand->find($id);

        return $brand_instance ? $brand_instance->deduction_percentage : 0;
    }

    public function magentoSoapUpdatePrices($product)
    {

        $options = array(
            'trace' => true,
            'connection_timeout' => 120,
            'wsdl_cache' => WSDL_CACHE_NONE,
            'exceptions' => 0,
        );
        $proxy = new \SoapClient(config('magentoapi.url'), $options);
        $sessionId = $proxy->login(config('magentoapi.user'), config('magentoapi.password'));

        $sku = $product->sku . $product->color;
//		$result = $proxy->catalogProductUpdate($sessionId, $sku , array('visibility' => 4));
        $data = [
            'price' => $product->price_eur_special,
            'special_price' => $product->price_eur_discounted
        ];

        $result = $proxy->catalogProductUpdate($sessionId, $sku, $data);


        return $result;
    }

    public function brandReference()
    {
        $brands = Brand::select('name','references')->where('magento_id', '>', 0)->get();
        foreach ($brands as $brand) {
            $referenceArray[] = $brand->name;
            if(!empty($brand->references)){
                $references = explode(';', $brand->references);
                if(is_array($references)){
                    foreach($references as $reference){
                        if($reference != null && $reference != ''){
                         $referenceArray[] = $reference;
                        }
                    }
                }
                
            }
        }

        
       return json_encode($referenceArray);

    }

    public function attachWebsite(Request $request)
    {
        $website = $request->get("website");
        $brandId = $request->get("brand_id");

        if(!empty($website) && !empty($brandId)) {
             
             if(is_array($website)) {
                StoreWebsiteBrand::where("brand_id",$brandId)->whereNotIn("store_website_id",$website)->delete();
                foreach ($website as $key => $web) {
                    $sbrands = StoreWebsiteBrand::where("brand_id",$brandId)
                     ->where("store_website_id",$web)
                     ->first();

                    if(!$sbrands)  {
                        $sbrands = new StoreWebsiteBrand;
                        $sbrands->brand_id = $brandId;
                        $sbrands->store_website_id = $web;
                        $sbrands->save();
                    }
                }

                return response()->json(["code" => 200 , "data" => [], "message" => "Website attached successfully"]);
             }else{
                return response()->json(["code" => 500 , "data" => [], "message" => "There is no website selected"]);
             }
        }

        return response()->json(["code" => 500 , "data" => [], "message" => "Oops, something went wrong"]);
    }

    public function createRemoteId(Request $request, $id)
    {
        $brand = \App\Brand::where("id",$id)->first();
        
        if(!empty($brand)) {
            if($brand->magento_id == '' || $brand->magento_id <= 0) {
                $brand->magento_id = 10000 + $brand->id;
                $brand->save(); 
                return response()->json(["code" => 200, "data" => $brand, "message" => "Remote id created successfully"]);
            }else{
                return response()->json(["code" => 500, "data" => $brand, "message" => "Remote id already exist"]);
            }
        }

        return response()->json(["code" => 500, "data" => $brand, "message" => "Brand not found"]);

    }

    public function changeSegment(Request $request) 
    {
        $id = $request->get("brand_id",0);
        $brand = \App\Brand::where("id",$id)->first();
        $segment = $request->get("segment");

        if($brand) {
           $brand->brand_segment = $segment;
           $brand->save();
           return response()->json(["code" => 200 , "data" => []]);
        }

        return response()->json(["code" => 500 , "data" => []]);
    }

    public function createSizeChart()
    {
        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');

        return view('brand.size-chart.create', ['brands' => $brands]);
    }
}
