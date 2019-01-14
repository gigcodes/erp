<?php

namespace App\Http\Controllers;

use App\Product;
use App\Setting;
use App\Stage;
use App\Category;
use App\Brand;
use Illuminate\Http\Request;

class ProductSupervisorController extends Controller
{
    public function __construct() {

	    $this->middleware('permission:supervisor-list',['only' => ['index']]);
	    $this->middleware('permission:supervisor-edit',['only' => ['edit','approve']]);
    }


	public function index(Stage $stage){

		$products = Product::latest()
		                   ->where('stage','>=', $stage->get('Attribute') )
		                   ->whereNull('dnf')
		                   ->paginate(Setting::get('pagination'));

		$roletype = 'Supervisor';

    $search_suggestions = [];
		$sku_suggestions = ( new Product() )->newQuery()->latest()->whereNotNull('sku')->select('sku')->get()->toArray();
		$brand_suggestions = Brand::getAll();

		foreach ($sku_suggestions as $key => $suggestion) {
			array_push($search_suggestions, $suggestion['sku']);
		}

		foreach ($brand_suggestions as $key => $suggestion) {
			array_push($search_suggestions, $suggestion);
		}

    $category_selection = Category::attr(['name' => 'category[]','class' => 'form-control select-multiple'])
		                                        ->selected(1)
		                                        ->renderAsDropdown();

		return view('partials.grid',compact('products','roletype', 'search_suggestions', 'category_selection'))
			->with('i', (request()->input('page', 1) - 1) * 10);

	}

	public function edit(Product $productsupervisor){

		return redirect( route('products.show',$productsupervisor->id) );
	}

	public function approve(Product $product,Stage $stage){

		$product->isApproved = 1;
		$product->stage = $stage->get('Supervisor');
		$product->save();

		NotificaitonContoller::store('has Approved',['ImageCropers'],$product->id);
		ActivityConroller::create($product->id,'supervisor','create');

		return back()->with('success', 'Product has been approved');

//		return ['msg'=>'success', 'isApproved'  => $product->isApproved ];
	}

	public function reject(Product $product,Request $request){

    	$this->validate($request,[
    		'role' => 'required',
		    'reason' => 'required',
	    ]);


		$role = $request->input('role');
		$reason = $request->input('reason');

		$product->rejected_note = $reason;
		$product->isApproved = -1;
		$product->save();


		NotificaitonContoller::store('has Rejected due to '.$reason,[$role],$product->id);
		ActivityConroller::create($product->id,'supervisor','reject');


		return back()->with( 'rejected', 'Product has been rejected' );

	}
}
