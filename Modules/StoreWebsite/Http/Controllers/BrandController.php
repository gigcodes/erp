<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\StoreWebsite;
use App\StoreWebsiteBrand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use seo2websites\MagentoHelper\MagentoHelper;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request, $id)
    {
        $title = "Attached Brand | Store Website";

        if ($request->ajax()) {
            // send response into the json
            $brands = \App\Brand::getBrands()->pluck("name", "id")->toArray();

            $storeWebsite = StoreWebsiteBrand::join("brands as b", "b.id", "store_website_brands.brand_id")
                ->where("store_website_id", $id)
                ->select(["store_website_brands.*", "b.name"])
                ->get();

            return response()->json([
                "code"             => 200,
                "store_website_id" => $id,
                "data"             => $storeWebsite,
                "brands"           => $brands,
            ]);
        }

        return view('storewebsite::index', compact('title'));
    }

    /**
     * store cateogories
     *
     */

    public function store(Request $request)
    {
        $storeWebsiteId = $request->get("store_website_id");
        $post           = $request->all();

        $validator = Validator::make($post, [
            'store_website_id' => 'required',
            'markup'           => 'required',
            'brand_id'         => 'unique:store_website_brands,brand_id,NULL,id,store_website_id,' . $storeWebsiteId . '|required',
        ]);

        if ($validator->fails()) {
            $outputString = "";
            $messages     = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . "<br>";
                }
            }
            return response()->json(["code" => 500, "error" => $outputString]);
        }

        $storeWebsiteBrand = new StoreWebsiteBrand();
        $storeWebsiteBrand->fill($post);
        $storeWebsiteBrand->save();

        return response()->json(["code" => 200, "data" => $storeWebsiteBrand]);

    }

    public function delete(Request $request, $id, $store_brand_id)
    {
        $storeBrand = StoreWebsiteBrand::where("store_website_id", $id)->where("id", $store_brand_id)->first();
        if ($storeBrand) {
            $storeBrand->delete();
        }
        return response()->json(["code" => 200, "data" => []]);
    }

    public function list(Request $request) {
        $title        = "Store Brand";
        $brands       = \App\Brand::query();
        
        if($request->keyword != null) {
            $brands = $brands->where("name","like","%".$request->keyword."%");
        }

        $brands = $brands->get();

        $product_counts = DB::table('products')->select('brand', DB::raw('count(*) as counts'))->groupBy('brand')->get();

        $storeWebsite = \App\StoreWebsite::all();
        $appliedQ      = \App\StoreWebsiteBrand::all();
        $apppliedResult = [];
        if(!$appliedQ->isEmpty()){
            foreach($appliedQ as $raw) {
                $apppliedResult[$raw->brand_id][] = $raw->store_website_id;
            }
        }

        return view("storewebsite::brand.index", compact(['title', 'brands', 'storeWebsite','apppliedResult', 'product_counts']));
    }

    public function pushToStore(Request $request)
    {
        if ($request->brand != null && $request->store != null) {
            $brandStore = \App\StoreWebsiteBrand::where("brand_id", $request->brand)->where("store_website_id", $request->store)->first();
            $website = \App\StoreWebsite::find($request->store);
            if($website){
                if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                    $brand = \App\Brand::find($request->brand);
                    $magentoBrandId = MagentoHelper::addBrand($brand,$website);
                }

            }

            if ($request->active == null || $request->active == "false") {
                if ($brandStore) {
                    $brandStore->delete();
                }
            } else {
                if (!$brandStore) {
                    $brandStore = new \App\StoreWebsiteBrand;
                }
                $brandStore->brand_id         = $request->brand;
                if(isset($magentoBrandId)){
                    $brandStore->magento_value = $magentoBrandId;
                }
                $brandStore->store_website_id = $request->store;
                $brandStore->save();
            }
        }

        return response()->json(["code" => 200, "data" => []]);

    }

    /**
     * run artisan command
     *
     */

    public function refreshMinMaxPrice()
    {
        try {
           
           \Artisan::call('brand:maxminprice'); 

            return response()->json('Console Commnad Ran',200);   
        
        } catch (\Exception $e) {
            
            return response()->json('Cannot call artisan command',200); 
        } 

    }

}
