<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MissingBrand;
use App\Brand;

class MissingBrandController extends Controller
{
    public function saveMissingBrand(Request $request)
    {
    	$name = $request->name;
    	$supplier = $request->supplier;
		if($name){
			$checkIfExist = MissingBrand::where('name',$name)->where('supplier',$supplier)->first();
			if($checkIfExist){
				return response()->json([
				    'message' => 'Missing Brand Already Exist'
				]);
			}else{
				$brand = new MissingBrand();
				$brand->name = $name;
				$brand->supplier = $supplier;
				$brand->save();
				return response()->json([
				    'message' => 'Missing Brand Saved'
				]);
			}
    	}else{
			return response()->json([
			    'message' => 'Please Send Brand Name'
			]);
    	}
    }


    public function index()
    {
    	$missingBrands = MissingBrand::orderBy('id','desc')->paginate(20);
    	$title = 'Missing Brands';
    	return view('missingbrand.index',['missingBrands' => $missingBrands,'title' => $title]);
    }

    public function store(Request $request, Brand $brand)
    {
        $data = $request->except('_token', '_method');

        $brand->name = $data['name'];
        $brand->save();

        $mBrand = MissingBrand::find($data['id']);
        if($mBrand) {
            $mBrand->delete();
        }

        return redirect()->back()->with('success', 'Brand added successfully');
    }

    public function reference(Request $request, Brand $brand)
    {

        $brand = $brand->find($request->brand);
        if($brand) {
            $ref = explode(",", $brand->references);
            $ref[]  = $request->name;
            $brand->references = implode(",",array_filter($ref));
            $brand->save();
        }

        $mBrand = MissingBrand::find($request->id);
        if($mBrand) {
            $mBrand->delete();
        }

        return redirect()->back()->with('success', 'Brand reference added successfully');

    }
}
