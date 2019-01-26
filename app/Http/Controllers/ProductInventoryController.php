<?php

namespace App\Http\Controllers;

use App\Product;
use App\Setting;
use App\Stage;
use App\Brand;
use App\Category;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InventoryImport;

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

		$category_selection = Category::attr(['name' => 'category[]','class' => 'form-control select-multiple'])
		                                        ->selected(1)
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

	public function instock(Request $request)
	{
		$data     = [];
		$term     = $request->input( 'term' );
		$data['term']     = $term;

		if ($request->brand[0] != null) {
			$productQuery = ( new Product() )->newQuery()->where('supplier', 'In-stock')
			                                 ->latest()->whereIn('brand', $request->brand);

			$data['brand'] = $request->brand[0];
		}

		if ($request->color[0] != null) {
			if ($request->brand[0] != null) {
				$productQuery = $productQuery->where('supplier', 'In-stock')->whereIn('color', $request->color);
			} else {
				$productQuery = ( new Product() )->newQuery()->where('supplier', 'In-stock')
				                                 ->latest()->whereIn('color', $request->color);
			}

			$data['color'] = $request->color[0];
		}

		if ($request->category[0] != 1) {
			$is_parent = Category::isParent($request->category[0]);
			$category_children = [];

			if ($is_parent) {
				$childs = Category::find($request->category[0])->childs()->get();

				foreach ($childs as $child) {
					$is_parent = Category::isParent($child->id);

					if ($is_parent) {
						$children = Category::find($child->id)->childs()->get();

						foreach ($children as $chili) {
							array_push($category_children, $chili->id);
						}
					} else {
						array_push($category_children, $child->id);
					}
				}
			} else {
				array_push($category_children, $request->category[0]);
			}

			if ($request->brand[0] != null || $request->color[0] != null) {
				$productQuery = $productQuery->where('supplier', 'In-stock')->whereIn('category', $category_children);
			} else {
				$productQuery = ( new Product() )->newQuery()->where('supplier', 'In-stock')
				                                 ->latest()->whereIn('category', $category_children);
			}

			$data['category'] = $request->category[0];
		}

		if ($request->price != null) {
			$exploded = explode(',', $request->price);
			$min = $exploded[0];
			$max = $exploded[1];

			if ($request->brand[0] != null || $request->color[0] != null || $request->category[0] != 1) {
				$productQuery = $productQuery->whereBetween('price_special', [$min, $max]);
			} else {
				$productQuery = ( new Product() )->newQuery()->where('supplier', 'In-stock')
				                                 ->latest()->whereBetween('price_special', [$min, $max]);
			}

			$data['price'][0] = $min;
			$data['price'][1] = $max;
		}

		if (trim($term) != '') {
			$productQuery = (( new Product() )->newQuery()->where('supplier', 'In-stock'))
			                                 ->latest()->where(function ($query) use ($term){
															 	    		return $query->orWhere( 'sku', 'LIKE', "%$term%" )
			                                 							->orWhere( 'id', 'LIKE', "%$term%" );
																									});


			if ( $term == - 1 ) {
				$productQuery = $productQuery->where('supplier', 'In-stock')->where(function ($query){
				 															return $query->orWhere( 'isApproved', - 1 );
									 });
			}

			if ( Brand::where('name', 'LIKE' ,"%$term%")->first() ) {
				$brand_id = Brand::where('name', 'LIKE' ,"%$term%")->first()->id;
				$productQuery = $productQuery->where('supplier', 'In-stock')->where(function ($query) use ($brand_id){
																			return $query->orWhere( 'brand', 'LIKE', "%$brand_id%" );});
			}

			if ( $category = Category::where('title', 'LIKE' ,"%$term%")->first() ) {
				$category_id = $category = Category::where('title', 'LIKE' ,"%$term%")->first()->id;
				$productQuery = $productQuery->where('supplier', 'In-stock')->where(function ($query) use ($term){
								return $query->orWhere( 'category', CategoryController::getCategoryIdByName( $term ));} );
			}

		} else {
			if ($request->brand[0] == null && $request->color[0] == null && $request->category[0] == null && $request->price[0] == null) {
				$productQuery = ( new Product() )->newQuery()
																				 ->where('supplier', 'In-stock')
				                                 ->latest();

			}
		}

		$search_suggestions = [];

		 $sku_suggestions = ( new Product() )->newQuery()->where('supplier', 'In-stock')
																			 ->latest()->whereNotNull('sku')->select('sku')->get()->toArray();

		$brand_suggestions = Brand::getAll();

		foreach ($sku_suggestions as $key => $suggestion) {
			array_push($search_suggestions, $suggestion['sku']);
		}

		foreach ($brand_suggestions as $key => $suggestion) {
			array_push($search_suggestions, $suggestion);
		}

		$data['search_suggestions'] = $search_suggestions;

		$selected_categories = $request->category ? $request->category : 1;

		$data['category_selection'] = Category::attr(['name' => 'category[]','class' => 'form-control'])
		                                        ->selected($selected_categories)
		                                        ->renderAsDropdown();


		$data['products'] = $productQuery->paginate( Setting::get( 'pagination' ) );

		$data['date'] = $request->date ? $request->date : '';
		$data['type'] = $request->type ? $request->type : '';
		$data['customer_id'] = $request->customer_id ? $request->customer_id : '';

		if ($request->ajax()) {
			$html = view('instock.product-items', $data)->render();
			return response()->json(['html' => $html]);
		}

		return view( 'instock.index', $data );
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

	public function import(Request $request)
	{
		$this->validate($request, [
			'file'	=> 'required|mimes:xlsx,xls'
		]);

		Excel::import(new InventoryImport, $request->file('file'));

		return back()->with('success', 'You have successfully imported Inventory');
	}
}
