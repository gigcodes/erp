<?php

namespace App\Http\Controllers;

use App\Helpers\StatusHelper;
use App\Product;
use App\Category;
use App\Setting;
use Illuminate\Http\Request;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

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

    public function crop(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required'
        ]);

        $product_id = $request->get('product_id');
        $product = Product::where('id', $product_id)->first();
        if ($product) {

            $media = $product->media()->first();
            
            $data['image'] = '';
            
            if($media) {
                $data['image'] = $media->getUrl();
                $data['media_id'] = $media->id;
                $data['product_id'] = $product_id;
            }
            
            if (!empty($data['image'])) {
                return view( 'google_search_image.crop', $data );
            }
        }

        return redirect()->back()->with('message','Image Not found!!');
    }

    public function searchImageOnGoogle(Request $request)
    {
        $this->validate($request, [
            'media_id' => 'required',
            'product_id' => 'required'
        ]);

        $product_id = $request->get('product_id');
        $product = Product::where('id', $product_id)->first();
        if ($product) {

            $media = $product->media()->first();
            
            if($media) {

                $path = $media->getAbsolutePath();
                $url = $media->getUrl();

                $img = \Image::make($media->getAbsolutePath());
                $height = $request->get('width', null);
                $width = $request->get('height', null);
                $x = $request->get('x', null);
                $y = $request->get('y', null);

                if ($height != null && $width != null && $x != null && $y != null) {
                    $img->crop($width[0], $height[0], $x[0], $y[0]);

                    if(!is_dir(public_path() . '/tmp_images')) {
                        mkdir(public_path() . '/tmp_images', 0777, true);
                    }                  
                    $path = public_path() . '/tmp_images/crop_'.$media->getBasenameAttribute();
                    $url = '/tmp_images/crop_'.$media->getBasenameAttribute();
                    $img->save($path);
                }
            }
            
        }
        
        if ($path) {
            $productImage = [];
            $productImage[$url] = GoogleVisionHelper::getImageDetails($path);
            $product = Product::where('id', $product_id)->first();
            return view( 'google_search_image.details', compact(['productImage', 'product_id', 'product']));
        } else {
            return redirect(route('google.search.image'))->with('message','Please Select Products');
        }

        /*
        $productIds = $request->get('product_ids');
        $productImage = [];
        if (is_array($productIds)) {
            $productArr = Product::whereIn('id', $productIds)->get();
            if ($productArr) {
                GoogleVisionHelper::setDebug( true );
                foreach ($productArr as $product) {
                	$media = $product->media()->first();
                	if($media) {
                    	$productImage[$media->getUrl()] = GoogleVisionHelper::getImageDetails($media->getAbsolutePath());
                    }
                }
	    		if(!empty($productImage)) {
	    			return view( 'google_search_image.details', compact(['productImage']));
	    		}
            }
        } else {
            return redirect(route('google.search.image'))->with('message','Please Select Products');
        }
        */

        abort(403, 'Sorry , it looks like there is no result from the request.');
    }

    public function details(Request $request)
    {
    	$url = $request->get("url");
        $product_id = $request->get("product_id");
    	$productImage = [];
    	if(!empty($url)) {
    		$productImage[$url] =  GoogleVisionHelper::getImageDetails($url);
    		if(!empty($productImage)) {
                $product = Product::where('id', $product_id)->first();
    			return view( 'google_search_image.details', compact(['productImage', 'product_id', 'product']));
    		}
		}

		abort(403, 'Sorry , it looks like there is no result from the request.');
    }

    public function product(Request $request)
    {

        if ($request->isMethod('post')) {

            $images = $request->get("images",[]);
            $productId = $request->get("product_id",0);

            $product = \App\Product::where("id",$productId)->first();

            if($product) {
                $imagesSave = false;
                if(!empty($images)) {
                    foreach($images as $image) {
                        $file = @file_get_contents($image);
                        if(!empty($file)) {
                            $media = MediaUploader::fromString($file)
                                        ->toDirectory('product'.DIRECTORY_SEPARATOR.floor($product->id / config('constants.image_per_folder')))
                                        ->useFilename(md5(date("Y-m-d H:i:s")))
                                        ->upload();
                            $product->attachMedia($media, config('constants.media_tags'));
                            $imagesSave = true;
                        }
                    }
                }

                $product->status_id = 22;
                if($imagesSave) {
                    StatusHelper::updateStatus($product, StatusHelper::$AI);
                    $product->status_id = 3;
                }

                $product->save();
            }

        }

        $productCount = \App\Product::where("status_id", StatusHelper::$unableToScrapeImages)->where("stock",">",0)->count();
        $product = \App\Product::where("status_id", StatusHelper::$unableToScrapeImages)->where("stock",">",0);

        if($request->has("supplier")) {
            $product = $product->where("supplier",$request->get("supplier"));
        }

        $product = $product->orderBy("id","desc")->first();

        $supplierList = \App\Product::where("status_id","14")->groupBy("supplier")
        ->select([\DB::raw("count(*) as supplier_count"),"supplier"])
        ->get()->toArray();

        $skippedSuppliers = \App\Product::where("status_id","22")->groupBy("supplier")
        ->select([\DB::raw("count(*) as supplier_count"),"supplier"])
        ->get()->toArray();

        return view("google_search_image.product",compact(['product', 'productCount', 'supplierList','skippedSuppliers']));
    }
}
