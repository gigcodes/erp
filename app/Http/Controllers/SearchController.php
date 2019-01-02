<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\Brand;
use App\Sale;
use App\Setting;
use App\Stage;
use Cache;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller {
	public function __construct() {
		$this->middleware( 'permission:product-list' );
	}

	public function search( Stage $stage, Request $request ) {

		$data     = [];
		$term     = $request->input( 'term' );
		$roletype = $request->input( 'roletype' );

		$data['term']     = $term;
		$data['roletype'] = $roletype;

		$doSelection = $request->input( 'doSelection' );

		if ( ! empty( $doSelection ) ) {

			$data['doSelection'] = true;
			$data['model_id']    = $request->input( 'model_id' );
			$data['model_type']  = $request->input( 'model_type' );

			$data['selected_products'] = ProductController::getSelectedProducts($data['model_type'],$data['model_id']);
		}

		if ($request->brand[0] != null) {
			$productQuery = ( new Product() )->newQuery()
			                                 ->latest()->whereIn('brand', $request->brand);

			$data['brand'] = $request->brand[0];
			Cache::put('filter-brand-' . Auth::id(), $data['brand'], 120);
		} else {
			// if (Cache::has('filter-brand')) {
			// 	$productQuery = ( new Product() )->newQuery()
			// 	                                 ->latest()->whereIn('brand', Cache::get('filter-brand'));
			//
			// 	$data['brand'] = Cache::get('filter-brand');
			// }
			Cache::forget('filter-brand-' . Auth::id());
		}

		if ($request->color[0] != null) {
			if ($request->brand[0] != null) {
				$productQuery = $productQuery->whereIn('color', $request->color);
			} else {
				$productQuery = ( new Product() )->newQuery()
				                                 ->latest()->whereIn('color', $request->color);
			}

			$data['color'] = $request->color[0];
			Cache::put('filter-color-' . Auth::id(), $data['color'], 120);
		} else {
			// if (Cache::has('filter-color')) {
			// 	if ($request->brand[0] != null) {
			// 		$productQuery = $productQuery->whereIn('color', Cache::get('filter-color'));
			// 	} else {
			// 		$productQuery = ( new Product() )->newQuery()
			// 		                                 ->latest()->whereIn('color', Cache::get('filter-color'));
			// 	}
			//
			// 	$data['color'] = Cache::get('filter-color');
			// }
			Cache::forget('filter-color-' . Auth::id());
		}

		// if ($request->category[0] == 1) {
		// 	if (Cache::has('filter-category')) {
		// 		$request->category[0] = Cache::get('filter-category');
		// 	}
		// }

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
				$productQuery = $productQuery->whereIn('category', $category_children);
			} else {
				$productQuery = ( new Product() )->newQuery()
				                                 ->latest()->whereIn('category', $category_children);
			}

			$data['category'] = $request->category[0];
			Cache::put('filter-category-' . Auth::id(), $data['category'], 120);
		} else {
			Cache::forget('filter-category-' . Auth::id());
		}

		if ($request->price != null) {
			$exploded = explode(',', $request->price);
			$min = $exploded[0];
			$max = $exploded[1];

			if ($min != 0 && $max != 10000000) {
				if ($request->brand[0] != null || $request->color[0] != null || $request->category[0] != 1) {
					$productQuery = $productQuery->whereBetween('price_special', [$min, $max]);
				} else {
					$productQuery = ( new Product() )->newQuery()
					                                 ->latest()->whereBetween('price_special', [$min, $max]);
				}
			}


			$data['price'][0] = $min;
			$data['price'][1] = $max;
			Cache::put('filter-price-' . Auth::id(), $request->price, 120);
		} else {
			// if (Cache::has('filter-price')) {
			// 	$exploded = explode(',', Cache::get('filter-price'));
			// 	$min = $exploded[0];
			// 	$max = $exploded[1];
			//
			// 	if ($min != 0 && $max != 10000000) {
			// 		if ($request->brand[0] != null || $request->color[0] != null || $request->category[0] != 1) {
			// 			$productQuery = $productQuery->whereBetween('price_special', [$min, $max]);
			// 		} else {
			// 			$productQuery = ( new Product() )->newQuery()
			// 			                                 ->latest()->whereBetween('price_special', [$min, $max]);
			// 		}
			// 	}
			//
			//
			// 	$data['price'][0] = $min;
			// 	$data['price'][1] = $max;
			// }
			Cache::forget('filter-price-' . Auth::id());
		}

		if ($request->supplier[0] != null) {
			if ($request->brand[0] != null || $request->color[0] != null || $request->category[0] != 1 || $request->price != "0,10000000") {
				$productQuery = $productQuery->whereIn('supplier', $request->supplier);
			} else {
				$productQuery = ( new Product() )->newQuery()
				                                 ->latest()->whereIn('supplier', $request->supplier);
			}

			$data['supplier'] = $request->supplier;
			Cache::put('filter-supplier-' . Auth::id(), $data['supplier'], 120);
		} else {
			// if (Cache::has('filter-supplier')) {
			// 	if ($request->brand[0] != null || $request->color[0] != null || $request->category[0] != 1 || $request->price != "0,10000000") {
			// 		$productQuery = $productQuery->whereIn('supplier', Cache::get('filter-supplier'));
			// 	} else {
			// 		$productQuery = ( new Product() )->newQuery()
			// 		                                 ->latest()->whereIn('supplier', Cache::get('filter-supplier'));
			// 	}
			//
			// 	$data['supplier'] = Cache::get('filter-supplier');
			// }
			Cache::forget('filter-supplier-' . Auth::id());
		}

		if ($request->quick_product === 'true') {
				$productQuery = ( new Product() )->newQuery()
				                                 ->latest()->where('quick_product', 1);
		}
		// return response()->json($request->all());
		if (trim($term) != '') {
			$productQuery = ( new Product() )->newQuery()
			                                 ->latest()
			                                 ->orWhere( 'sku', 'LIKE', "%$term%" )
			                                 ->orWhere( 'id', 'LIKE', "%$term%" )//		                                 ->orWhere( 'category', $term )
			;

			if ( $term == - 1 ) {
				$productQuery = $productQuery->orWhere( 'isApproved', - 1 );
			}

			// BrandController::getBrandIds( $term )
			if ( Brand::where('name', 'LIKE' ,"%$term%")->first() ) {
				$brand_id = Brand::where('name', 'LIKE' ,"%$term%")->first()->id;
				$productQuery = $productQuery->orWhere( 'brand', 'LIKE', "%$brand_id%" );
			}

			// CategoryController::getCategoryIdByName( $term )
			if ( $category = Category::where('title', 'LIKE' ,"%$term%")->first() ) {
				$category_id = $category = Category::where('title', 'LIKE' ,"%$term%")->first()->id;
				$productQuery = $productQuery->orWhere( 'category', CategoryController::getCategoryIdByName( $term ) );
			}

			if ( ! empty( $stage->getIDCaseInsensitive( $term ) ) ) {

				$productQuery = $productQuery->orWhere( 'stage', $stage->getIDCaseInsensitive( $term ) );
			}

			if ( ! ( \Auth::user()->hasRole( [ 'Admin', 'Supervisors' ] ) ) ) {

				$productQuery = $productQuery->where( 'stage', '>=', $stage->get( $roletype ) );
			}

			if ( $roletype != 'Selection' && $roletype != 'Searcher' ) {

				$productQuery = $productQuery->whereNull( 'dnf' );
			}
		} else {
			if ($request->brand[0] == null && $request->color[0] == null && $request->category[0] == null && $request->price[0] == null && $request->quick_product !== 'true') {
				$productQuery = ( new Product() )->newQuery()
				                                 ->latest()
				                                 ->orWhere( 'sku', 'LIKE', "%$term%" )
				                                 ->orWhere( 'id', 'LIKE', "%$term%" )//		                                 ->orWhere( 'category', $term )
				;
			}
		}

		$search_suggestions = [];
		// $product_suggestions = ( new Product() )->newQuery()
		// 																 ->latest()->whereNotNull('name')->select('name')->get()->toArray();

		 $sku_suggestions = ( new Product() )->newQuery()
																			 ->latest()->whereNotNull('sku')->select('sku')->get()->toArray();
		// foreach ($product_suggestions as $key => $suggestion) {
		// 	array_push($search_suggestions, $suggestion['name']);
		// }
		$brand_suggestions = Brand::getAll();
		// dd($brand_suggestions);
		foreach ($sku_suggestions as $key => $suggestion) {
			array_push($search_suggestions, $suggestion['sku']);
		}

		foreach ($brand_suggestions as $key => $suggestion) {
			array_push($search_suggestions, $suggestion);
		}

		// dd($search_suggestions);
		$data['search_suggestions'] = $search_suggestions;

		$selected_categories = $request->category ? $request->category : 1;

		$data['category_selection'] = Category::attr(['name' => 'category[]','class' => 'form-control'])
		                                        ->selected($selected_categories)
		                                        ->renderAsDropdown();


		$data['products'] = $productQuery->paginate( Setting::get( 'pagination' ) );

		if ($request->ajax()) {
			$html = view('partials.image-load', ['products' => $data['products'], 'data'	=> $data])->render();
			// $pagination = $data['products']->appends($request->except('page'))->links();
			return response()->json(['html' => $html]);
		}

		return view( 'partials.grid', $data );
	}


	public function getPendingProducts( $roletype ) {

		$stage    = new Stage();
		$stage_no = intval( $stage->getID( $roletype ) );

		$products = Product::latest()
		                   ->where( 'stage', $stage_no - 1 )
		                   ->where( 'isApproved', '!=', - 1 )
		                   ->whereNull( 'dnf' )
		                   ->whereNull( 'deleted_at' )
		                   ->paginate( Setting::get( 'pagination' ) );

		return view( 'partials.grid', compact( 'products', 'roletype' ) );
	}
}
