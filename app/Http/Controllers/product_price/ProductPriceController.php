<?php

namespace App\Http\Controllers\product_price;

use App\Http\Controllers\Controller;
use App\CountryGroup;
use App\StoreWebsite;
use App\Product;
use App\Setting;
use App\SimplyDutyCountry;
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
        $countryGroups = SimplyDutyCountry::getSelectList();
        $product_list = [];
        if( $request->country_code && $request->product ){
                $storeWebsites = StoreWebsite::select('title', 'id','website')->get()->toArray();
                $product       = Product::where( 'id', $request->product )->orWhere( 'sku', $request->product )->first();
            
                if(empty( $product )){
                    return redirect()->back()->with('error','No product found');
                }
                foreach ($storeWebsites as $key => $value) {
                    $price = $product->getPrice( $value['id'], $request->country_code );
                    $product_list[] = [
                        'getPrice'     => $price,
                        'id'           => $product->id,
                        'sku'          => $product->sku,
                        'brand'        => $product->brands->name,
                        'segment'      => $product->brands->brand_segment,
                        'website'      => $value['website'],
                        'eur_price'    => $product->price_eur_special,
                        'seg_discount' => $product->price_eur_discounted,
                        'iva'          => Product::getIvaPrice($product->price),
                        'add_duty'     => $product->getDuty( $request->country_code ),
                        'add_profit'   => $price['promotion'],
                        'final_price'  => $price['total'],
                    ];
                }
        }

        return view('product_price.index',compact('countryGroups','product_list'));
    }

}
