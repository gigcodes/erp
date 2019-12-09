<?php

namespace App\Http\Controllers;

use App\Helpers\StatusHelper;
use App\Product;
use App\Category;
use App\ScrapeQueues;
use App\Setting;
use App\Brand;
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
        $data     = [];
        $term     = $request->input( 'term' );
        $quickProduct  = request("quick_product",!empty($request->all()) ? false : "true");
        $request->request->add(["quick_product" => $quickProduct]);
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

                if ( $term == - 1 ) {
                    $query->orWhere( 'isApproved', - 1 );
                }

                if ( Brand::where('name', 'LIKE' ,"%$term%")->first() ) {
                    $brand_id = Brand::where('name', 'LIKE' ,"%$term%")->first()->id;
                    $query->orWhere( 'brand', 'LIKE', "%$brand_id%" );
                }

                if ( $category = Category::where('title', 'LIKE' ,"%$term%")->first() ) {
                    $category_id = $category = Category::where('title', 'LIKE' ,"%$term%")->first()->id;
                    $query->orWhere( 'category', $category_id);
                }
            });
        }

        $data["quick_product"] = false;
        if ($quickProduct === 'true') {
        	$data["quick_product"] = true;
            $productQuery = $productQuery->where('quick_product', 1);
        }

        $selected_categories = $request->category ? $request->category : 1;

        $data['category_selection'] = Category::attr(['name' => 'category[]','class' => 'form-control select-multiple2'])
                                                ->selected($selected_categories)
                                                ->renderAsDropdown();

        if ($request->get('shoe_size', false)) {
            $productQuery = $productQuery->where('products.size', 'like', "%".$request->get('shoe_size')."%");
        }

        if (!empty($request->quick_sell_groups) && is_array($request->quick_sell_groups)) {
            $productQuery = $productQuery->whereRaw("(id in (select product_id from product_quicksell_groups where quicksell_group_id in (".implode(",", $request->quick_sell_groups).") ))");
        }

        $data[ 'products' ] = $productQuery->join("mediables", function ($query) {
                                $query->on("mediables.mediable_id", "products.id")->where("mediable_type", "App\Product");
                            })
                            ->groupBy('products.id')
                            ->paginate( Setting::get( 'pagination' ) );

        $data['locations'] = (new \App\ProductLocation())->pluck('name');
        $data['quick_sell_groups'] = \App\QuickSellGroup::select('id', 'name')->orderBy('id', 'desc')->get();
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
                $imageWidth = $img->width();
                $imageHeight = $img->height();
                $width = $request->get('width', null);
                $height = $request->get('height', null);
                $x = $request->get('x', null);
                $y = $request->get('y', null);

                if ($height != null && $width != null && $x != null && $y != null) {

                    //Checking if width and height are same
                    if($imageWidth != $width[0] || $imageHeight != $height[0]){

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

            $images = $request->post("images",[]);
            $productId = $request->post("product_id",0);

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

        $revise = $request->get("revise",0);

        $products = \App\Product::where("products.stock",">",0);

        if($revise == 1) {
            $products->where("status_id", StatusHelper::$manualImageUpload);
        }else{
            $products->where("status_id", StatusHelper::$unableToScrapeImages);
        }

        if($request->has("supplier")) {
            $products = $products->join('product_suppliers as ps',"ps.product_id","products.id");
            $products = $products->where("ps.supplier_id",$request->get("supplier"));
        }

        $productCount = $products->count();

        $product = $products->select(["products.*"])->orderBy("products.id","desc")->first();

        $supplierList = \App\Product::where("status_id","14")
        ->where("products.stock",">",0)
        ->join('product_suppliers as ps',"ps.product_id","products.id")
        ->join('suppliers as s',"s.id","ps.supplier_id")
        ->groupBy("s.id")
        ->select([\DB::raw("count(*) as supplier_count"),"s.supplier","s.id"])
        ->get()->toArray();

        $skippedSuppliers = \App\Product::where("status_id","22")
        ->where("products.stock",">",0)
        ->join('product_suppliers as ps',"ps.product_id","products.id")
        ->join('suppliers as s',"s.id","ps.supplier_id")
        ->groupBy("s.id")
        ->select([\DB::raw("count(*) as supplier_count"),"s.supplier","s.id"])
        ->get()->toArray();

        return view("google_search_image.product",compact(['product', 'productCount', 'supplierList','skippedSuppliers']));
    }

    public function queue(Request $request) {
        // Update product status
        $product = Product::findOrFail($request->product_id);

        // Update product
        $product->status_id = StatusHelper::$queuedForGoogleImageSearch;
        $product->save();

        // Create queue item
        $scrapeQueue = new ScrapeQueues();
        $scrapeQueue->product_id = (int) $request->product_id;
        $scrapeQueue->url = $request->url;
        $scrapeQueue->save();

        return redirect("/google-search-image")->with('message', 'Product is queued');
    }

    public function getImageForMultipleProduct(Request $request){

        $product = Product::findOrFail($request->id);
        $product->status_id = StatusHelper::$isBeingScrapedWithGoogleImageSearch;
        $product->save();
        
        $media = $product->media()->first();

        if($media){
            $count = 0;
            $urls = GoogleVisionHelper::getImageDetails($media->getUrl());

            foreach($urls['pages'] as $url){
                echo $url . "\n";
                if(stristr($url, '.gucci.') || stristr($url, '.farfetch.')){
                    // Create queue item
                    $scrapeQueue = new ScrapeQueues();
                    $scrapeQueue->product_id = (int) $product->id;
                    $scrapeQueue->url = $url;
                    $scrapeQueue->save();
                    $count++;
                    break;
                }

            }
            if($count == 0){
               $product->status_id = StatusHelper::$googleImageSearchFailed;
               $product->save();
            } else {
                StatusHelper::updateStatus($product, StatusHelper::$AI);
            }
        }
        return response()->json(['success' => 'true'], 200);
    }

}
