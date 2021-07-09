<?php

namespace App\Http\Controllers\product_price;

use App\Http\Controllers\Controller;
use App\CountryGroup;
use App\StoreWebsite;
use App\Product;
use App\Setting;
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
        $product_list = [];
        if( $request->country_code){
                $storeWebsites = StoreWebsite::select('title', 'id','website')->where("is_published","1")->get()->toArray();
                if(strtolower($request->random) == "yes" && empty($request->product)) {
                    $products = Product::where( 'status_id', StatusHelper::$finalApproval)->groupBy('category')->limit(50)->latest()->get();
                }else{
                    $products = Product::where( 'id', $request->product )->orWhere( 'sku', $request->product )->get();
                }

                if($products->isEmpty()){
                    return redirect()->back()->with('error','No product found');
                }

                foreach ($storeWebsites as $key => $value) {
                    foreach($products as $product) {
                        $dutyPrice = $product->getDuty( $request->country_code );
                        $price = $product->getPrice( $value['id'], $request->country_code,null, true,$dutyPrice);
                        $ivaPercentage = \App\Product::IVA_PERCENTAGE;

                        $product_list[] = [
                            'getPrice'     => $price,
                            'id'           => $product->id,
                            'sku'          => $product->sku,
                            'brand'        => ($product->brands) ? $product->brands->name : "-",
                            'segment'      => ($product->brands) ? $product->brands->brand_segment : "-",
                            'website'      => $value['website'],
                            'eur_price'    => number_format($price['original_price'],2,'.',''),
                            'seg_discount' => (float)$price['segment_discount'],
                            'iva'          => \App\Product::IVA_PERCENTAGE."%",
                            'add_duty'     => $product->getDuty( $request->country_code)."%",
                            'add_profit'   => number_format($price['promotion'],2,'.',''),
                            'final_price'  => number_format($price['total'],2,'.',''),
                        ];
                    }
                }
        }

        return view('product_price.index',compact('countryGroups','product_list'));
    }

}
