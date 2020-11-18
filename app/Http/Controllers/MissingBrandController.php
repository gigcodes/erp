<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MissingBrand;

class MissingBrandController extends Controller
{
    public function saveMissingBrand(Request $request)
    {
    	$name = $request->name;
		if($name){
			$checkIfExist = MissingBrand::where('name',$name)->first();
			if($checkIfExist){
				return response()->json([
				    'message' => 'Missing Brand Already Exist'
				]);
			}else{
				$brand = new MissingBrand();
				$brand->name = $name;
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
}
