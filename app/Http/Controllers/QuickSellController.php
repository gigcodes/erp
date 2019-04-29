<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Setting;
use App\Category;
use App\Brand;
use App\ReadOnly\LocationList;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class QuickSellController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if ($request->brand[0] != null) {
  			$products = (new Product())->newQuery()
  			                                 ->latest()->where('quick_product', 1)->whereIn('brand', $request->brand);

  			$brand = $request->brand;
  		}

      if ($request->category != 1) {
  			$is_parent = Category::isParent($request->category);
  			$category_children = [];

  			if ($is_parent) {
  				$childs = Category::find($request->category)->childs()->get();

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
  				array_push($category_children, $request->category);
  			}

  			if ($request->brand[0] != null) {
  				$products = $products->whereIn('category', $category_children);
  			} else {
  				$products = (new Product())->newQuery()
  				                                 ->latest()->where('quick_product', 1)->whereIn('category', $category_children);
  			}

  			$category = $request->category;
  		}

      if ($request->location[0] != null) {
        if ($request->brand[0] != null || $request->category != 1) {
  				$products = $products->whereIn('location', $request->location);
  			} else {
  				$products = (new Product())->newQuery()
  				                                 ->latest()->where('quick_product', 1)->whereIn('location', $request->location);
  			}

  			$location = $request->location[0];
      }

      if ($request->brand[0] == null && ($request->category == null || $request->category == 1) && $request->location[0] == null) {
        $products = (new Product())->newQuery()
                                         ->latest()->where('quick_product', 1);
      }

      $products = $products->paginate(Setting::get('pagination'));
      $brands_all = Brand::all();
      $categories_all = Category::all();
      $brands = [];
      $categories = [];

      foreach ($brands_all as $brand) {
        $brands[$brand->id] = $brand->name;
      }

      foreach ($categories_all as $category) {
        $categories[$category->id] = $category->title;
      }

      $category_selection = Category::attr(['name' => 'category','class' => 'form-control', 'id'  => 'category_selection'])
  		                                        ->renderAsDropdown();

      $selected_categories = $request->category ? $request->category : 1;

  		$filter_categories_selection = Category::attr(['name' => 'category','class' => 'form-control', 'id' => 'filter_categories_selection'])
  		                                        ->selected($selected_categories)
  		                                        ->renderAsDropdown();

      $locations = (new LocationList)->all();

      return view('quicksell.index', [
        'products'  => $products,
        'brands'  => $brands,
        'categories'  => $categories,
        'category_selection'  => $category_selection,
        'brand'         => $brand,
        'category'      => $category,
        'location'      => $location ?? '',
        'filter_categories_selection'  => $filter_categories_selection,
        'locations'  => $locations
      ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request,[
  			'sku'    => 'sometimes|unique:products',
  			'images.*' => 'required | mimes:jpeg,bmp,png,jpg',
  		]);

      if ($request->hasfile('images')) {
        foreach ($request->file('images') as $image) {
          $product = new Product();

          if ($request->sku) {
            $product->sku = $request->sku;
          } else {
            $product->sku = $this->generateRandomSku();
          }

      		$product->quick_product = 1;
      		$product->save();

          $filename = str_slug($image->getClientOriginalName());
      		$media = MediaUploader::fromSource($image)->useFilename($filename)->upload();
      		$product->attachMedia($media,config('constants.media_tags'));
        }
      }

      return redirect()->route('quicksell.index')->with('success', 'You have successfully uploaded image');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $this->validate($request,[
  			'images.*' => 'sometimes | mimes:jpeg,bmp,png,jpg',
  		]);

      $product = Product::find($id);

      $product->supplier = $request->supplier;
      $product->price = $request->price;
      $product->size = $request->size;
      $product->brand = $request->brand;
      $product->location = $request->location;
      $product->category = $request->category;

      if(!empty($product->brand) && !empty($product->price)) {
  			$product->price_inr     = app('App\Http\Controllers\ProductSelectionController')->euroToInr($product->price, $product->brand);
  			$product->price_special = app('App\Http\Controllers\ProductSelectionController')->calculateSpecialDiscount($product->price_inr, $product->brand);
  		} else {
  			$product->price_special = $request->price_special;
  		}

      $product->save();

      if ($request->hasfile('images')) {
        foreach ($request->file('images') as $image) {
          $filename = str_slug($image->getClientOriginalName());
      		$media = MediaUploader::fromSource($image)->useFilename($filename)->upload();
      		$product->attachMedia($media,config('constants.media_tags'));
        }
      }

      return redirect()->route('quicksell.index')->with('success', 'You have successfully updated Quick Product');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function generateRandomSku()
    {
      $sku = Product::where('sku', 'LIKE', "%QCKPRO%")->latest()->select(['sku'])->first();

      if ($sku) {
        $exploded = explode('-', $sku->sku);
        $new_sku = 'QCKPRO-' . (intval( $exploded[1] ) + 1);

  			return $new_sku;
      }

      return 'QCKPRO-000001';
    }
}
