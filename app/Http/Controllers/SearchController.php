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
		//$this->middleware( 'permission:product-list' );
	}

	public function search( Stage $stage, Request $request ) {
		$data     = [];
		$term     = $request->input( 'term' );
		$roletype = $request->input( 'roletype' );
		$model_type = $request->input( 'model_type' );
		$data['customer_id'] = $request->input('customer_id');

		$data['term']     = $term;
		$data['roletype'] = $roletype;
		$perPageLimit = $request->get("per_page",Setting::get( 'pagination' ));
		$sourceOfSearch = $request->get("source_of_search","na");

		// start add fixing for the price range since the one request from price is in range 
		// price  = 0 , 100

		$priceRange = $request->get("price",null);

		if($priceRange && !empty($priceRange)) {
			@list($minPrice, $maxPrice) = explode(",",$priceRange);
			// adding min price
			if(isset($minPrice)) {
				$request->request->add(['price_min' =>  (float) $minPrice]);
			}
			// addin max price
			if(isset($maxPrice)) {
				$request->request->add(['price_max' => (float) $maxPrice]);
			}
		}


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
			Cache::forget('filter-brand-' . Auth::id());
		}



		if ($request->color[0] != null) {
            if ( isset($productQuery) ) {
				$productQuery = $productQuery->whereIn('color', $request->color);
			} else {
				$productQuery = ( new Product() )->newQuery()
				                                 ->latest()->whereIn('color', $request->color);
			}

			$data['color'] = $request->color[0];
			Cache::put('filter-color-' . Auth::id(), $data['color'], 120);
		} else {
			Cache::forget('filter-color-' . Auth::id());
		}



		if ($request->category[0] != null && $request->category[0] != 1) {
			$category_children = [];

			foreach ($request->category as $category) {
				$is_parent = Category::isParent($category);

				if ($is_parent) {
					$childs = Category::find($category)->childs()->get();

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
					array_push($category_children, $category);
				}
			}

            if ( isset($productQuery) ) {
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


        if ( $request->price_min != null ) {
            if ( isset($productQuery) ) {
                $productQuery = $productQuery->where( 'price_special', '>=', $request->price_min );
            } else {
                $productQuery = ( new Product() )->newQuery()
                    ->latest()->where( 'price_special', '>=', $request->price_min );
            }
            Cache::put( 'filter-price-min-' . Auth::id(), $request->price_min, 120 );
        } else {
            Cache::forget( 'filter-price-min-' . Auth::id() );
        }

        if ( $request->price_max != null ) {
            if ( isset($productQuery) ) {
                $productQuery = $productQuery->where( 'price_special', '<=', $request->price_max );
            } else {
                $productQuery = ( new Product() )->newQuery()
                    ->latest()->where( 'price_special', '<=', $request->price_max );
            }
            Cache::put( 'filter-price-max-' . Auth::id(), $request->price_max, 120 );
        } else {
            Cache::forget( 'filter-price-max-' . Auth::id() );
        }

        //dd($productQuery->get());
		if ($request->supplier[0] != null) {
			$suppliers_list = implode(',', $request->supplier);

            if ( isset($productQuery) ) {
				$productQuery = $productQuery->with('Suppliers')->whereRaw("products.id in (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($suppliers_list))");
			} else {
				$productQuery = ( new Product() )->newQuery()->with('Suppliers')
				                                 ->latest()->whereRaw("products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($suppliers_list))");
			}

			$data['supplier'] = $request->supplier;
			Cache::put('filter-supplier-' . Auth::id(), $data['supplier'], 120);
		} else {
			Cache::forget('filter-supplier-' . Auth::id());
		}

		if (trim($request->size) != '') {
			if ( isset($productQuery) ) {
				$productQuery = $productQuery->whereNotNull('size')->where(function ($query) use ($request) {
					 $query->where('size', $request->size)->orWhere('size', 'LIKE', "%$request->size,")->orWhere('size', 'LIKE', "%,$request->size,%");
				 });
				// ->where('size', 'LIKE', "%$request->size%");
			} else {
				$productQuery = ( new Product() )->newQuery()
																			 ->latest()->whereNotNull('size')->where(function ($query) use ($request) {
																 					$query->where('size', $request->size)->orWhere('size', 'LIKE', "%$request->size,")->orWhere('size', 'LIKE', "%,$request->size,%");
																 				});
																			 // ->where('size', 'LIKE', "%$request->size%");
			}

			$data['size'] = $request->size;
			Cache::put('filter-size-' . Auth::id(), $data['size'], 120);
		} else {
			Cache::forget('filter-size-' . Auth::id());
		}

		if ($request->location[0] != null) {
            if ( isset($productQuery) ) {
				$productQuery = $productQuery->whereIn('location', $request->location);
			} else {
				$productQuery = ( new Product() )->newQuery()
				                                 ->latest()->whereIn('location', $request->location);
			}

			$data['location'] = $request->location[0];
		}

		if ($request->type[0] != null) {
            if ( isset($productQuery) ) {
				if (count($request->type) > 1) {
					$productQuery = $productQuery->where('is_scraped', 1)->orWhere('status', 2);
				} else {
					if ($request->type[0] == 'scraped') {
						$productQuery = $productQuery->where('is_scraped', 1);
					} elseif ($request->type[0] == 'imported') {
						$productQuery = $productQuery->where('status', 2);
					} else {
						$productQuery = $productQuery->where('isUploaded', 1);
					}
				}
			} else {
				if (count($request->type) > 1) {
					$productQuery = ( new Product() )->newQuery()
																				 ->latest()->where('is_scraped', 1)->orWhere('status', 2);
				} else {
					if ($request->type[0] == 'scraped') {
						$productQuery = ( new Product() )->newQuery()
																					 ->latest()->where('is_scraped', 1);
					} elseif ($request->type[0] == 'imported') {
						$productQuery = ( new Product() )->newQuery()
																					 ->latest()->where('status', 2);
					} else {
						$productQuery = ( new Product() )->newQuery()
																					 ->latest()->where('isUploaded', 1);
					}
				}
			}

			$data['type'] = $request->type[0];
		}

		if ($request->date != '') {
            if ( isset($productQuery) ) {
				if ($request->type[0] != null && $request->type[0] == 'uploaded') {
					$productQuery = $productQuery->where('is_uploaded_date', 'LIKE', "%$request->date%");
				} else {
					$productQuery = $productQuery->where('created_at', 'LIKE', "%$request->date%");
				}
			} else {
				$productQuery = ( new Product() )->newQuery()
																			 ->latest()->where('created_at', 'LIKE', "%$request->date%");
			}

			$data['date'] = $request->date;
			Cache::put('filter-date-' . Auth::id(), $data['date'], 120);
		} else {
			Cache::forget('filter-date-' . Auth::id());
		}

		if ($request->quick_product === 'true') {
				$productQuery = ( new Product() )->newQuery()
				                                 ->latest()->where('quick_product', 1);
		}

		if (trim($term) != '') {
			$productQuery = ( new Product() )->newQuery()
			                                 ->latest()
			                                 ->orWhere( 'sku', 'LIKE', "%$term%" )
			                                 ->orWhere( 'id', 'LIKE', "%$term%" )//		                                 ->orWhere( 'category', $term )
			;

			if ( $term == - 1 ) {
				$productQuery = $productQuery->orWhere( 'isApproved', - 1 );
			}

			if ( Brand::where('name', 'LIKE' ,"%$term%")->first() ) {
				$brand_id = Brand::where('name', 'LIKE' ,"%$term%")->first()->id;
				$productQuery = $productQuery->orWhere( 'brand', 'LIKE', "%$brand_id%" );
			}

			if ( $category = Category::where('title', 'LIKE' ,"%$term%")->first() ) {
				$category_id = $category = Category::where('title', 'LIKE' ,"%$term%")->first()->id;
				$productQuery = $productQuery->orWhere( 'category', CategoryController::getCategoryIdByName( $term ) );
			}

//			if ( ! empty( $stage->getIDCaseInsensitive( $term ) ) ) {
//
//				$productQuery = $productQuery->orWhere( 'stage', $stage->getIDCaseInsensitive( $term ) );
//			}

//			if ( ! ( \Auth::user()->hasRole( [ 'Admin', 'Supervisors' ] ) ) ) {
//
//				$productQuery = $productQuery->where( 'stage', '>=', $stage->get( $roletype ) );
//			}

			if ($roletype != 'Selection' && $roletype != 'Searcher') {

				$productQuery = $productQuery->whereNull( 'dnf' );
			}
		} else {
			if ($request->brand[0] == null && $request->color[0] == null && ($request->category[0] == null || $request->category[0] == 1) && $request->price == "0,400000" && $request->supplier[0] == null && trim($request->size) == '' && $request->date == '' && $request->type[0] == null && $request->location[0] == null) {
				$productQuery = (new Product())->newQuery()->latest();
			}
		}

		if ($request->ids[0] != null) {
			$productQuery = (new Product())->newQuery()
																		 ->latest()->whereIn('id', $request->ids);
		}

		// $search_suggestions = [];
		//
		//  $sku_suggestions = ( new Product() )->newQuery()
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

		 // fix if query is not setup due to some unknow condition                                        
		if(!isset($productQuery)) {
			$productQuery = ( new Product() )->newQuery()->latest();
		}

		// assing product to varaible so can use as per condition for join table media
		$products = $productQuery->where('stock', '>=', 1);

		// if source is attach_media for search then check product has image exist or not
		if($sourceOfSearch == "attach_media") {
			$products = $products->join("mediables",function($query){
				$query->on("mediables.mediable_id" , "products.id")->where("mediable_type", "App\Product");
			});
		}
		
		// select fields..
		$products = $products->select(['id', 'sku', 'size', 'price_special', 'brand', 'supplier', 'purchase_status', 'isApproved', 'stage', 'status', 'is_scraped', 'created_at']);
		
		$data['is_on_sale'] = 0;
		if ($request->get('is_on_sale') == 'on') {
		    $data['is_on_sale'] = 1;
		    $products = $products->where('is_on_sale', 1);
        }

		$products_count = $products->count();
		$data['products_count'] = $products_count;
		$data['products'] = $products->paginate( $perPageLimit );

		if ($request->model_type == 'broadcast-images') {
			$data['attachImages'] = true;
			$data['doSelection'] = false;
		}

		if ($request->ajax()) {
			$html = view('partials.image-load', ['products' => $data['products'], 'data'	=> $data, 'selected_products' => ($request->selected_products ? json_decode($request->selected_products) : []), 'model_type' => $model_type])->render();

			return response()->json(['html' => $html, 'products_count' => $products_count]);
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

		 $category_selection = Category::attr(['name' => 'category[]','class' => 'form-control'])
			                                        ->renderAsDropdown();

		return view( 'partials.grid', compact( 'products', 'roletype', 'category_selection' ) );
	}
}
