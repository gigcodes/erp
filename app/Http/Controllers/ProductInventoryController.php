<?php

namespace App\Http\Controllers;

use App\Product;
use App\Setting;
use App\Stage;
use App\Brand;
use App\Category;
use Illuminate\Http\Request;

class ProductInventoryController extends Controller
{
	public function __construct() {

		$this->middleware('permission:inventory-list',['only' => ['index']]);
		$this->middleware('permission:inventory-edit',['only' => ['edit','stock']]);
	}


	public function index(Stage $stage){

		$products = Product::latest()
		                   ->where('stage','>=',$stage->get('Approver') )
		                   ->whereNull('dnf')
		                   ->paginate(Setting::get('pagination'));

		$roletype = 'Inventory';

		$search_suggestions = [];
		$sku_suggestions = ( new Product() )->newQuery()->latest()->whereNotNull('sku')->select('sku')->get()->toArray();
		$brand_suggestions = Brand::getAll();

		foreach ($sku_suggestions as $key => $suggestion) {
			array_push($search_suggestions, $suggestion['sku']);
		}

		foreach ($brand_suggestions as $key => $suggestion) {
			array_push($search_suggestions, $suggestion);
		}

		$category_selection = Category::attr(['name' => 'category[]','class' => 'form-control'])
		                                        ->selected(1)->multiple()
		                                        ->renderAsDropdown();

		return view('partials.grid',compact('products','roletype', 'search_suggestions', 'category_selection'))
			->with('i', (request()->input('page', 1) - 1) * 10);

	}

	public function edit(Product $productlister){

		return redirect( route('products.show',$productlister->id) );
	}

	public function stock(Product $product,Request $request,Stage $stage){


		$this->validate($request,[
			'stock' => 'required|numeric|min:0'
		]);

		$result = $this->magentoSoapUpdateStock($product,$request->input( 'stock' ));

		if( $result ) {
			$product->stock = $request->input( 'stock' );
			$product->stage = $stage->get( 'Inventory' );
			$product->save();

			//		NotificaitonContoller::store('has Final Approved',['Admin'],$product->id);
			ActivityConroller::create( $product->id, 'inventory', 'create' );

			return back()->with( 'success', 'Product inventory has been updated' );
		}

		return back()->with('error','Error Occured while uploading stock');
	}

	public function magentoSoapUpdateStock($product,$stockQty){

		$options = array(
			'trace' => true,
			'connection_timeout' => 120,
			'wsdl_cache' => WSDL_CACHE_NONE,
		);
		$proxy = new \SoapClient(config('magentoapi.url'), $options);
		$sessionId = $proxy->login(config('magentoapi.user'), config('magentoapi.password'));

		$sku = $product->sku . $product->color;

//		$result = $proxy->catalogProductUpdate($sessionId, $sku , array('visibility' => 4));

		if(!empty($product->size)){

			$sizes_array = explode( ',', $product->size );

			foreach ($sizes_array as $size)
				$result = $proxy->catalogInventoryStockItemUpdate( $sessionId, $sku . '-' . $size, array(
					'qty'         => $stockQty,
					'is_in_stock' => $stockQty ? 1 : 0
				) );

			$result = $proxy->catalogInventoryStockItemUpdate( $sessionId, $sku, array(
//				'qty'         => 0,
				'is_in_stock' => $stockQty ? 1 : 0
			) );
		}
		else {
			$result = $proxy->catalogInventoryStockItemUpdate( $sessionId, $sku, array(
				'qty'         => $stockQty,
				'is_in_stock' => $stockQty ? 1 : 0
			) );
		}

		return $result;
	}
}
