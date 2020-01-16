<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplier;
use App\Brand;

class SupplierSearchController extends Controller
{
    public function index(Request $request)
    {   
        $selectedBrand = Brand::where('id', $request->input('brand'))->get()->first();
        $brands = Brand::whereNotNull('magento_id')->get()->all();
    	$supplier = Supplier::whereNotNull('scraped_brands_raw')->get()->all();
        $requestBrand = $selectedBrand->name;
        if(!empty($request->supplier) && !empty($request->brand)) {
            $supplier = Supplier::where('supplier', 'like', '%' . $request->supplier . '%')->where('scraped_brands_raw', 'like', '%' . $selectedBrand->name . '%')->get()->all();
        } elseif (!empty($request->brand)) {
           $reference = array_unique(explode(';', $selectedBrand->references));
           if (empty($reference)) {
               $supplier= Supplier:: where('scraped_brands_raw', 'like', '%' . $selectedBrand->name . '%')->get()->all();
           } else {
                foreach ($reference as $key => $value) {
                    $supplier= Supplier:: where('scraped_brands_raw', 'like', '%' . $selectedBrand->name . '%')->orWhere('scraped_brands_raw', 'like', '%' . $value . '%')->get()->all();
                }
           }
        } else if(!empty($request->supplier)) {
            $supplier = Supplier::whereNotNull('scraped_brands_raw')->where('supplier', 'like', '%' . $request->supplier . '%')->get()->all();
        }
        else { 
            $supplier = Supplier::whereNotNull('scraped_brands_raw')->get()->all();     
        }
    	return view('suppliers.supplier-search', compact('supplier','brands','requestBrand'));
    }
}
