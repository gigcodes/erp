<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use App\Setting;
use Illuminate\Http\Request;

use seo2websites\GoogleVision\GoogleVisionHelper;

class GoogleSearchImageController extends Controller
{
    /**
     * Display a Google Search Image
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$fileName = 'D:\pravin_project\soloux\sololux-erp\public\uploads\0a2243e085dfaf7ae30db984b3ae2129.jpeg';
        
        $data     = [];
        $term     = $request->input( 'term' );
        $data['term']     = $term;

        $productQuery = ( new Product() )->newQuery()->latest();
        if ($request->brand[0] != null) {
            $productQuery = $productQuery->whereIn('brand', $request->brand);
            $data['brand'] = $request->brand[0];
        }

        if ($request->color[0] != null) {
            $productQuery = $productQuery->whereIn('color', $request->color);
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

            $productQuery = $productQuery->whereIn('category', $category_children);

            $data['category'] = $request->category[0];
        }

        if (isset($request->price) && $request->price != null) {
            $exploded = explode(',', $request->price);
            $min = $exploded[0];
            $max = $exploded[1];

            if ($min != '0' || $max != '10000000') {
                $productQuery = $productQuery->whereBetween('price_special', [$min, $max]);
            }

            $data['price'][0] = $min;
            $data['price'][1] = $max;
        }

        if ($request->location[0] != null) {
            $productQuery = $productQuery->whereIn('location', $request->location);
            $data['location'] = $request->location[0];
        }

        if ($request->no_locations) {
            $productQuery = $productQuery->whereNull('location');
        }

        if (trim($term) != '') {
            $productQuery = $productQuery->where(function ($query) use ($term){
                $query->orWhere( 'sku', 'LIKE', "%$term%" )
                      ->orWhere( 'id', 'LIKE', "%$term%" );
            });


            if ( $term == - 1 ) {
                $productQuery = $productQuery->where(function ($query){
                                                                            return $query->orWhere( 'isApproved', - 1 );
                                     });
            }

            if ( Brand::where('name', 'LIKE' ,"%$term%")->first() ) {
                $brand_id = Brand::where('name', 'LIKE' ,"%$term%")->first()->id;
                $productQuery = $productQuery->where(function ($query) use ($brand_id){
                                                                            return $query->orWhere( 'brand', 'LIKE', "%$brand_id%" );});
            }

            if ( $category = Category::where('title', 'LIKE' ,"%$term%")->first() ) {
                $category_id = $category = Category::where('title', 'LIKE' ,"%$term%")->first()->id;
                $productQuery = $productQuery->where(function ($query) use ($term){
                                return $query->orWhere( 'category', CategoryController::getCategoryIdByName( $term ));} );
            }

        }

        $selected_categories = $request->category ? $request->category : 1;

        $data['category_selection'] = Category::attr(['name' => 'category[]','class' => 'form-control select-multiple2'])
                                                ->selected($selected_categories)
                                                ->renderAsDropdown();
        
        if ($request->get('shoe_size', false)) {
            $productQuery = $productQuery->where('products.size', 'like', "%".$request->get('shoe_size')."%");
        }

        $data[ 'products' ] = $productQuery->join("mediables", function ($query) {
                                $query->on("mediables.mediable_id", "products.id")->where("mediable_type", "App\Product");
                            })
                            ->groupBy('products.id')
                            ->paginate( Setting::get( 'pagination' ) );
        
        $data['locations'] = (new \App\ProductLocation())->pluck('name');

    return view( 'google_search_image.index', $data );
    }

    

    public function searchImageOnGoogle(Request $request)
    {
        $this->validate($request, [
            'product_ids' => 'required'
        ]);

        $productIds = $request->get('product_ids');
        $productImage = [];
        if (is_array($productIds)) {
            $productArr = Product::whereIn('id', $productIds)->get();
            if ($productArr) {
                GoogleVisionHelper::setDebug( true );
                foreach ($productArr as $product) {
                	$media = $product->media()->first();
                	if($media) {
                    	$result = GoogleVisionHelper::getImageDetails($media->getAbsolutePath());
			    		if(!empty($result)) {
			    			return view( 'google_search_image.details', compact(['result']));
			    		}
                    }                    
                }
            }
        } else {
            return redirect()->back()->with('message','Please Select Products');
        }

        abort(403, 'Sorry , it looks like there is no result from the request.');
    }

    public function details(Request $request)
    {
    	$url = $request->get("url");
    	
    	if(!empty($url)) {
    		$result = GoogleVisionHelper::getImageDetails($url);
    		if(!empty($result)) {
    			return view( 'google_search_image.details', compact(['result']));
    		}
		}

		abort(403, 'Sorry , it looks like there is no result from the request.');
    } 
}
