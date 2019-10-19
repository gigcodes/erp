<?php

namespace App\Http\Controllers;

use App\ApiKey;
use App\Customer;
use App\ProductQuicksellGroup;
use App\QuickSellGroup;
use Illuminate\Http\Request;
use App\Product;
use App\Setting;
use App\Category;
use App\Brand;
use App\Supplier;
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

      $products = Product::where('quick_product',1)->where('is_pending',0)->latest()->paginate(Setting::get('pagination'));
      $allSize  = Product::where('quick_product',1)->where('is_pending',0)->groupBy("size")->select("size")->pluck("size")->toArray();
      
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
      $suppliers = Supplier::select(['id', 'supplier'])->get();

      $category_tree = [];
  		$categories_array = [];

  		foreach (Category::all() as $category) {
  			if ($category->parent_id != 0) {
  				$parent = $category->parent;
  				if ($parent->parent_id != 0) {
  					$category_tree[$parent->parent_id][$parent->id][$category->id];
  				} else {
  					$category_tree[$parent->id][$category->id] = $category->id;
  				}
  			}

  			$categories_array[$category->id] = $category->parent_id;
  		}

      $new_category_selection = Category::attr(['name' => 'category','class' => 'form-control', 'id' => 'product-category'])
  		                                        ->renderAsDropdown();
        $api_keys = ApiKey::select('number')->get();
        $customers = Customer::orderBy('name','asc')->get();

      return view('quicksell.index', [
        'products'  => $products,
        'brands'  => $brands,
        'categories'  => $categories,
        'category_selection'  => $category_selection,
        'brand'         => $brand,
        'category'      => $category,
        'location'      => $location ?? '',
        'suppliers'      => $suppliers,
        'filter_categories_selection'  => $filter_categories_selection,
        'locations'  => $locations,
        'category_tree'  => $category_tree,
        'categories_array'  => $categories_array,
        'new_category_selection'  => $new_category_selection,
        'api_keys' =>  $api_keys,
        'customers' => $customers,
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
  			'sku'    => 'required|unique:products',
  			'images.*' => 'required | mimes:jpeg,bmp,png,jpg',
  		]);

        $product = new Product;

        $product->name = $request->name;
  		$product->sku = $request->sku;
  		$product->size = $request->size ? implode(',', $request->size) : $request->other_size;
  		$product->brand = $request->brand;
  		$product->color = $request->color;
  		$product->supplier = $request->supplier;
  		$product->location = $request->location;
  		$product->category = $request->category;
  		$product->price = $request->price;
  		$product->stock = 1;
        $product->quick_product = 1;

  		$brand = Brand::find($request->brand);

  		if ($request->price) {
  			if(isset($request->brand) && !empty($brand->euro_to_inr))
  				$product->price_inr = $brand->euro_to_inr * $product->price;
  			else
  				$product->price_inr = Setting::get('euro_to_inr') * $product->price;

  			$product->price_inr = round($product->price_inr, -3);
  			$product->price_special = $product->price_inr - ($product->price_inr * $brand->deduction_percentage) / 100;

  			$product->price_special = round($product->price_special, -3);
  		}

  		$product->save();

  		if ($request->supplier != '') {
        $supplier = Supplier::where('supplier', $request->supplier)->first();
  			$product->suppliers()->attach($supplier); // In-stock ID
  		}

      if ($request->hasfile('images')) {
        foreach ($request->file('images') as $image) {
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
    public function saveGroup(Request $request){
        if($request->type != null && $request->products){
        if($request->type == 1){
            foreach ($request->products as $id){
                $group = new ProductQuicksellGroup();
                $group->product_id = $id;
                $group->quicksell_group_id = $request->group;
                $group->save();
            }
        }else{
            $group = QuickSellGroup::orderBy('id', 'desc')->first();
            if ($group != null) {
                $group_create =  new QuickSellGroup();
                $incrementId = ($group->group+1);
                $group_create->group = $incrementId;
                $group_create->save();
                $group_id = $group_create->group;
            } else {
                $group =  new QuickSellGroup();
                $group->group = 1;
                $group->save();
                $group_id = $group->group;
            }
            foreach ($request->products as $id){
                $group = new ProductQuicksellGroup();
                $group->product_id = $id;
                $group->quicksell_group_id = $group_id;
                $group->save();
            }
        }
        }else{
            return redirect()->route('quicksell.index')->with('success', 'Failed saving Quick Product Group');
        }


        return redirect()->route('quicksell.index')->with('success', 'You have successfully saved Quick Product Group');

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pending(Request $request)
    {
        if ($request->brand[0] != null) {
            $products = (new Product())->newQuery()
                ->where('quick_product', 1)->whereIn('brand', $request->brand);

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
                    ->where('quick_product', 1)->whereIn('category', $category_children);
            }

            $category = $request->category;
        }

        if ($request->location[0] != null) {
            if ($request->brand[0] != null || $request->category != 1) {
                $products = $products->whereIn('location', $request->location);
            } else {
                $products = (new Product())->newQuery()
                    ->where('quick_product', 1)->whereIn('location', $request->location);
            }

            $location = $request->location[0];
        }

        if ($request->brand[0] == null && ($request->category == null || $request->category == 1) && $request->location[0] == null) {
            $products = (new Product())->newQuery()
                ->where('quick_product', 1);
        }

        $products = $products->where('is_pending',1)->latest()->paginate(Setting::get('pagination'));
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
        $suppliers = Supplier::select(['id', 'supplier'])->get();

        $category_tree = [];
        $categories_array = [];

        foreach (Category::all() as $category) {
            if ($category->parent_id != 0) {
                $parent = $category->parent;
                if ($parent->parent_id != 0) {
                    $category_tree[$parent->parent_id][$parent->id][$category->id];
                } else {
                    $category_tree[$parent->id][$category->id] = $category->id;
                }
            }

            $categories_array[$category->id] = $category->parent_id;
        }

        $new_category_selection = Category::attr(['name' => 'category','class' => 'form-control', 'id' => 'product-category'])
            ->renderAsDropdown();

        return view('quicksell.pending', [
            'products'  => $products,
            'brands'  => $brands,
            'categories'  => $categories,
            'category_selection'  => $category_selection,
            'brand'         => $brand,
            'category'      => $category,
            'location'      => $location ?? '',
            'suppliers'      => $suppliers,
            'filter_categories_selection'  => $filter_categories_selection,
            'locations'  => $locations,
            'category_tree'  => $category_tree,
            'categories_array'  => $categories_array,
            'new_category_selection'  => $new_category_selection,
        ]);
    }

    public function activate(Request $request){

        $product = Product::findorfail($request->id);
        $product->is_pending = 0;
        $product->update();
        return redirect()->route('quicksell.pending')->with('success', 'You have activated Quick Product');
    }

    public function search(Request $request)
    {

        if($request->selected_products || $request->term  || $request->category || $request->brand || $request->color || $request->supplier ||
            $request->location || $request->size || $request->price ){

            $query  = Product::query();
            if (request('term') != null) {
                $query->where('sku', '=', request('term',0))
                    ->orWhere('supplier', 'LIKE', request('term',0))
                    ->orWhereHas('brands', function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->term}%");
                    })
                    ->orWhereHas('product_category', function ($qu) use ($request) {
                    $qu->where('title', 'like', "%{$request->term}%");
                    });
            }
            if (request('category') != null) {
                $query->whereIn('category', request('category',0));
            }
            if (request('brand') != null) {
                $query->whereIn('brand', request('brand'));
            }
            if (request('color') != null) {
                $query->whereIn('color', request('color'));
            }
            if (request('supplier') != null) {
                $query->whereIn('supplier', request('supplier'));
            }
            if (request('location') != null) {
                $query->where('location','LIKE', request('location',0));
            }
            if (request('size') != null) {
                $query->where('size','LIKE', request('size'));
            }
            if (request('price') != null) {
                $price = (explode(",",$request->price));
                $from = $price[0];
                $to = $price[1];
                $query->whereBetween('price',[ $from , $to ]);
            }

            if(request('per_page') != null){
                $per_page = request('per_page');
            }else{
                $per_page = Setting::get('pagination');
            }

            $products = $query->where('quick_product',1)->paginate($per_page);
        }else{
            $products = Product::where('is_pending',0)->latest()->paginate(Setting::get('pagination'));
        }


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
        $suppliers = Supplier::select(['id', 'supplier'])->get();

        $category_tree = [];
        $categories_array = [];

        foreach (Category::all() as $category) {
            if ($category->parent_id != 0) {
                $parent = $category->parent;
                if ($parent->parent_id != 0) {
                    $category_tree[$parent->parent_id][$parent->id][$category->id];
                } else {
                    $category_tree[$parent->id][$category->id] = $category->id;
                }
            }

            $categories_array[$category->id] = $category->parent_id;
        }

        $new_category_selection = Category::attr(['name' => 'category','class' => 'form-control', 'id' => 'product-category'])
            ->renderAsDropdown();
        $api_keys = ApiKey::select('number')->get();
        $customers = Customer::orderBy('name','asc')->get();

        return view('quicksell.index', [
            'products'  => $products,
            'brands'  => $brands,
            'categories'  => $categories,
            'category_selection'  => $category_selection,
            'brand'         => $brand,
            'category'      => $category,
            'location'      => $location ?? '',
            'suppliers'      => $suppliers,
            'filter_categories_selection'  => $filter_categories_selection,
            'locations'  => $locations,
            'category_tree'  => $category_tree,
            'categories_array'  => $categories_array,
            'new_category_selection'  => $new_category_selection,
            'api_keys' =>  $api_keys,
            'customers' => $customers,
        ]);
    }



}
