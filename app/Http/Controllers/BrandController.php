<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Setting;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    //

	public function __construct() {
		$this->middleware('permission:brand-edit',[ 'only' => 'index','create','store','destroy','update','edit']);
	}

	public function index(){

		$brands = Brand::oldest()->whereNull('deleted_at')->paginate(Setting::get('pagination'));

		return view('brand.index',compact('brands'))
					->with('i', (request()->input('page', 1) - 1) * 10);
	}

	public  function create(){

		$data['name'] = '';
		$data['euro_to_inr'] = '';
		$data['deduction_percentage'] = '';
		$data['magento_id'] = '';

		$data['modify'] = 0;

		return view('brand.form',$data);
	}


	public function edit(Brand $brand){

		$data = $brand->toArray();
		$data['modify'] = 1;

		return view('brand.form',$data);
	}


	public function store(Request $request, Brand $brand){

		$this->validate($request,[
			'name' => 'required',
			'euro_to_inr' => 'required|numeric',
			'deduction_percentage' => 'required|numeric',
			'magento_id' => 'required|numeric',
		]);

		$data = $request->except('_token','_method');

		$brand->create($data);

		return redirect()->route('brand.index')->with('success','Brand added successfully');
	}

	public function update(Request $request, Brand $brand){

		$this->validate($request,[
			'name' => 'required',
			'euro_to_inr' => 'required|numeric',
			'deduction_percentage' => 'required|numeric',
			'magento_id' => 'required|numeric',
		]);

		$data = $request->except(['_token','_method']);

		foreach ($data as $key => $value)
			$brand->$key = $value;

		$brand->update();

		return redirect()->route('brand.index')->with('success','Brand updated successfully');
	}

	public function destroy(Brand $brand){

		$brand->delete();
		return redirect()->route('brand.index')->with('success','Brand Deleted successfully');

	}

	public static function getBrandName($id){

		$brand = new Brand();
		$brand_instance = $brand->find($id);

		return $brand_instance ? $brand_instance->name : '';
	}

	public static function getBrandIds($term){

		$brand = Brand::where('name', '=' ,$term)->first();

		return $brand ? $brand->id : 0;
	}

	public static function getEuroToInr($id){

		$brand = new Brand();
		$brand_instance = $brand->find($id);

		return $brand_instance ? $brand_instance->euro_to_inr : 0;
	}

	public static function getDeductionPercentage($id){

		$brand = new Brand();
		$brand_instance = $brand->find($id);

		return $brand_instance ? $brand_instance->deduction_percentage : 0;
	}
}
