<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Setting;
use App\Product;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    //

    public function __construct()
    {
      //  $this->middleware('permission:brand-edit', ['only' => 'index', 'create', 'store', 'destroy', 'update', 'edit']);
    }

    public function index()
    {

        $brands = Brand::oldest()->whereNull('deleted_at')->paginate(Setting::get('pagination'));

        return view('brand.index', compact('brands'))
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
                $product->price_special = $product->price_inr - ($product->price_inr * $brand->deduction_percentage) / 100;

                $product->price_special = round($product->price_special, -3);

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
            'price' => $product->price_inr,
            'special_price' => $product->price_special
        ];

        $result = $proxy->catalogProductUpdate($sessionId, $sku, $data);


        return $result;
    }
}
