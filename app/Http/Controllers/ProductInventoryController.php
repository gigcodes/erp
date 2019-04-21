<?php

namespace App\Http\Controllers;

use App\Product;
use App\Setting;
use App\Stage;
use App\Brand;
use App\Category;
use App\Helpers;
use App\ReadOnly\LocationList;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InventoryImport;
use Carbon\Carbon;

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
											 ->select(['id', 'sku', 'size', 'price_special', 'brand', 'supplier', 'isApproved', 'stage', 'status', 'is_scraped', 'created_at'])
		                   ->paginate(Setting::get('pagination'));

		$roletype = 'Inventory';

		$category_selection = Category::attr(['name' => 'category[]','class' => 'form-control select-multiple'])
		                                        ->selected(1)
		                                        ->renderAsDropdown();

		return view('partials.grid',compact('products','roletype', 'category_selection'))
			->with('i', (request()->input('page', 1) - 1) * 10);

	}

	public function list(Request $request, Stage $stage)
	{
		$category_tree = [];

		foreach (Category::all() as $category) {
			if ($category->parent_id != 0) {
				$parent = $category->parent;
				if ($parent->parent_id != 0) {
					$category_tree[$parent->parent_id][$parent->id][$category->id];
				} else {
					$category_tree[$parent->id][$category->id] = 0;
				}
			}
		}

		// dd($category_tree);

		$brands_array = Brand::getAll();
		$products_brands = Product::latest()
		                   ->where('stage','>=',$stage->get('Approver') )
											 ->whereNull('dnf')
											 ->where('stock', '>=', 1)->get()
											 ->groupBy([function ($query) use ($brands_array) {
												 if (isset($brands_array[$query->brand])) {
													 return $brands_array[$query->brand];
												 }

												 return 'Unknown Brand';
											 }, 'supplier', 'category']);

		// dd($products_brands);

		$inventory_data = [];

		foreach ($products_brands as $brand_name => $suppliers) {
			foreach ($suppliers as $supplier_name => $categories) {
				$tree = [];
				$inventory_data[$brand_name][$supplier_name] = $category_tree;

				foreach ($categories as $category_id => $products) {
					$category = Category::find($category_id);
					if ($category->parent_id != 0) {
						$parent = $category->parent;
						if ($parent->parent_id != 0) {
							$inventory_data[$brand_name][$supplier_name][$parent->parent_id][$parent->id] += count($products);
						} else {
							$inventory_data[$brand_name][$supplier_name][$parent->id][$category->id] += count($products);
						}
					}
				}
			}
		}

		// dd($inventory_data);

		$categories_array = [];
		$categories = Category::all();

		foreach ($categories as $category) {
			$categories_array[$category->id] = $category->title;
		}

 		return view('products.list',compact('inventory_data', 'categories_array'))
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
		$product->stock = $request->input( 'stock' );
		$product->stage = $stage->get( 'Inventory' );
		$product->save();

		if( $result ) {

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

		if (isset($request->category) && $request->category[0] != 1) {
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
				$productQuery = (new Product())->newQuery()->where('supplier', 'In-stock')
				                                 ->latest()->whereIn('category', $category_children);
			}

			$data['category'] = $request->category[0];
		}

		if (isset($request->price) && $request->price != null) {
			$exploded = explode(',', $request->price);
			$min = $exploded[0];
			$max = $exploded[1];

			if ($min != '0' || $max != '10000000') {
				if ($request->brand[0] != null || $request->color[0] != null || $request->category[0] != 1) {
					$productQuery = $productQuery->whereBetween('price_special', [$min, $max]);
				} else {
					$productQuery = ( new Product() )->newQuery()->where('supplier', 'In-stock')
					                                 ->latest()->whereBetween('price_special', [$min, $max]);
				}
			}

			$data['price'][0] = $min;
			$data['price'][1] = $max;
		}

		if ($request->location[0] != null) {
			if ($request->brand[0] != null || $request->color[0] != null || $request->category[0] != 1 || $request->price != "0,10000000") {
				$productQuery = $productQuery->whereIn('location', $request->location);

			} else {
				$productQuery = ( new Product() )->newQuery()->latest()->where('supplier', 'In-stock')
				                                 ->whereIn('location', $request->location);
			}
			$data['location'] = $request->location[0];
		}

		if ($request->no_locations) {
			if ($request->brand[0] != null || $request->color[0] != null || $request->category[0] != 1 || $request->price != "0,10000000" || $request->location[0] != null) {
				$productQuery = $productQuery->whereNull('location');

			} else {
				$productQuery = ( new Product() )->newQuery()->latest()->where('supplier', 'In-stock')
				                                 ->whereNull('location');
			}
			$data['no_locations'] = true;
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
			if ($request->brand[0] == null && $request->color[0] == null && (!isset($request->category) || $request->category[0] == 1) && (!isset($request->price) || $request->price == "0,10000000") && $request->location[0] == null && !isset($request->no_locations)) {
				$productQuery = ( new Product() )->newQuery()
																				 ->where('supplier', 'In-stock')
				                                 ->latest();

			}
		}

		// $search_suggestions = [];
		//
		// $sku_suggestions = ( new Product() )->newQuery()->where('supplier', 'In-stock')
		// 																	 ->latest()->whereNotNull('sku')->select('sku')->get()->toArray();
		//
		// $brand_suggestions = Brand::getAll();
		//
		// foreach ($sku_suggestions as $key => $suggestion) {
		// 	array_push($search_suggestions, $suggestion['sku']);
		// }
		//
		// foreach ($brand_suggestions as $key => $suggestion) {
		// 	array_push($search_suggestions, $suggestion);
		// }
		//
		// $data['search_suggestions'] = $search_suggestions;

		$selected_categories = $request->category ? $request->category : 1;

		$data['category_selection'] = Category::attr(['name' => 'category[]','class' => 'form-control'])
		                                        ->selected($selected_categories)
		                                        ->renderAsDropdown();


		$data['products'] = $productQuery->paginate( Setting::get( 'pagination' ) );

		$data['date'] = $request->date ? $request->date : '';
		$data['type'] = $request->type ? $request->type : '';
		$data['customer_id'] = $request->customer_id ? $request->customer_id : '';

		$data['locations'] = (new LocationList)->all();

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
		$result = false;

//		$result = $proxy->catalogProductUpdate($sessionId, $sku , array('visibility' => 4));

		if(!empty($product->size)){

			$sizes_array = explode( ',', $product->size );

			foreach ($sizes_array as $size) {
				$error_message = '';

				try {
					$result = $proxy->catalogInventoryStockItemUpdate( $sessionId, $sku . '-' . $size, array(
						'qty'         => $stockQty,
						'is_in_stock' => $stockQty ? 1 : 0
					) );
				} catch (\Exception $e) {
					$error_message = $e->getMessage();
				}

				if ($error_message == 'Product not exists.') {
          $product->isUploaded = 0;
          $product->isFinal = 0;
					$product->save();
				}
			}

			try {
				$result = $proxy->catalogInventoryStockItemUpdate( $sessionId, $sku, array(
	//				'qty'         => 0,
					'is_in_stock' => $stockQty ? 1 : 0
				) );
			} catch (\Exception $e) {
				$error_message = $e->getMessage();
			}

			if ($error_message == 'Product not exists.') {
				$product->isUploaded = 0;
				$product->isFinal = 0;
				$product->save();
			}
		}
		else {
			try {
				$result = $proxy->catalogInventoryStockItemUpdate( $sessionId, $sku, array(
					'qty'         => $stockQty,
					'is_in_stock' => $stockQty ? 1 : 0
				) );
			} catch (\Exception $e) {
				$error_message = $e->getMessage();
			}

			if ($error_message == 'Product not exists.') {
				$product->isUploaded = 0;
				$product->isFinal = 0;
				$product->save();
			}
		}

		return $result;
	}

	public function import(Request $request)
	{
		$this->validate($request, [
			'file'	=> 'required'
		]);

		$array = (new InventoryImport)->toArray($request->file('file'));

		$new_array = [];
		$brands_array = Helpers::getUserArray(Brand::all());

		foreach ($array[0] as $key => $item) {
			$new_array[$item['modellovariante']][] = $item;
		}

		foreach ($new_array as $sku => $items) {
			$formatted_sku = str_replace(' ', '', $sku);

			if ($product = Product::where('sku', $formatted_sku)->first()) {
				if (in_array($items[0]['brand'], $brands_array)) {
					if (count($items) > 1) {
						$sizes = '';
						$product->stock = 1;
						$product->import_date = Carbon::now();
						$product->status = 3; // Import Update status

						foreach ($items as $key => $item) {
							$size = str_replace('½', '.5', $item['taglia']);

							if ($key == 0) {
								$sizes .= $size;
							} else {
								$sizes .= "," . $size;
							}
						}

						if (!preg_match('/UNI/', $sizes)) {
							$product->size = $sizes;
						}

						$product->save();
					} else {
						$product->stock = 1;
						$product->import_date = Carbon::now();
						$product->status = 3; // Import Update status

						foreach ($items as $key => $item) {
							$size = str_replace('½', '.5', $item['taglia']);
						}

						if (!preg_match('/UNI/', $size)) {
							$product->size = $size;
						}

						$product->save();
					}
				}
			} else {
				if (in_array($items[0]['brand'], $brands_array)) {
					if (count($items) > 1) {
						$sizes = '';
						$product = new Product;
						$product->sku = $formatted_sku;
						$product->brand = array_search($items[0]['brand'], $brands_array);
						$product->stage = 3;
						$product->stock = 1;
						$product->import_date = Carbon::now();
						$product->status = 2; // Import Create status

						foreach ($items as $key => $item) {
							$size = str_replace('½', '.5', $item['taglia']);

							if ($key == 0) {
								$sizes .= $size;
							} else {
								$sizes .= "," . $size;
							}
						}

						if (!preg_match('/UNI/', $sizes)) {
							$product->size = $sizes;
						}

						$product->save();
					} else {
						$product = new Product;
						$product->sku = $formatted_sku;
						$product->brand = array_search($items[0]['brand'], $brands_array);
						$product->stage = 3;
						$product->stock = 1;
						$product->import_date = Carbon::now();
						$product->status = 2; // Import Create status

						foreach ($items as $key => $item) {
							$size = str_replace('½', '.5', $item['taglia']);
						}

						if (!preg_match('/UNI/', $size)) {
							$product->size = $sizes;
						}

						$product->save();
					}
				}
			}
		}

		return back()->with('success', 'You have successfully imported Inventory');
	}
}
