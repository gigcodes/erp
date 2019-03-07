<?php


namespace App\Http\Controllers;


use App\Category;
use App\Order;
use App\OrderProduct;
use App\Product;
use App\ScrapedProducts;
use App\Sale;
use App\Setting;
use App\Sizes;
use App\Brand;
use App\Stock;
use Cache;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class ProductController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	function __construct() {
		$this->middleware( 'permission:product-list', [ 'only' => [ 'show' ] ] );
//		$this->middleware('permission:product-create', ['only' => ['create','store']]);
//		$this->middleware('permission:product-edit', ['only' => ['edit','update']]);

//		$this->middleware('permission:product-delete', ['only' => ['destroy']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		if ($request->archived == 'true') {
			$products = Product::onlyTrashed()->latest()->select(['id', 'sku', 'name']);
		} else {
			$products = Product::latest()->select(['id', 'sku', 'name']);
		}
		$term = $request->term;
		$archived = $request->archived;

		if(!empty($term)){
			$products = $products->where(function ($query) use ($term){
				return $query
						->orWhere('id','like','%'.$term.'%')
						->orWhere('name','like','%'.$term.'%')
						->orWhere('sku','like','%'.$term.'%')
				;
			});
		}

		$products = $products->paginate(Setting::get('pagination'));

		return view('products.index', compact( 'products', 'term', 'archived'))
			->with('i', (request()->input('page', 1) - 1) * 10);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Product $product
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show( Product $product, Sizes $sizes ) {
		$data = [];

		$data['dnf']               = $product->dnf;
		$data['id']                = $product->id;
		$data['name']              = $product->name;
		$data['short_description'] = $product->short_description;

		$data['measurement_size_type'] = $product->measurement_size_type;
		$data['lmeasurement']          = $product->lmeasurement;
		$data['hmeasurement']          = $product->hmeasurement;
		$data['dmeasurement']          = $product->dmeasurement;

		$data['size']        = $product->size;
		$data['size_value']  = $product->size_value;
		$data['sizes_array'] = $sizes->all();

		$data['composition'] = $product->composition;
		$data['sku']         = $product->sku;
		$data['made_in']     = $product->made_in;
		$data['brand']       = $product->brand;
		$data['color']       = $product->color;
		$data['price']       = $product->price;
//		$data['price'] = $product->inr;
		$data['euro_to_inr']   = $product->euro_to_inr;
		$data['price_inr']     = $product->price_inr;
		$data['price_special'] = $product->price_special;

		$data['isApproved']    = $product->isApproved;
		$data['rejected_note'] = $product->rejected_note;
		$data['isUploaded']    = $product->isUploaded;
		$data['isFinal']       = $product->isFinal;
		$data['stock']         = $product->stock;
		$data['reason']        = $product->rejected_note;

		$data['product_link']     = $product->product_link;
		$data['supplier']    = $product->supplier;
		$data['supplier_link']    = $product->supplier_link;
		$data['description_link'] = $product->description_link;

		$data['images'] = $product->getMedia( config( 'constants.media_tags' ) );

		$data['categories'] = $product->category ? CategoryController::getCategoryTree( $product->category ) : '';

		$data['has_reference'] = ScrapedProducts::where('sku', $product->sku)->first() ? true : false;

		return view( 'partials.show', $data );
	}

	public function archive($id) {
		$product = Product::find($id);
		$product->delete();

		return redirect()->back()
		                 ->with( 'success', 'Product archived successfully' );
	}

	public function restore($id) {
		$product = Product::withTrashed()->find($id);
		$product->restore();

		return redirect()->back()
		                 ->with( 'success', 'Product restored successfully' );
	}

	public function destroy($id) {
		$product = Product::find($id);
		$product->forceDelete();

		return redirect()->back()
		                 ->with( 'success', 'Product deleted successfully' );
	}

	public function attachProducts( $model_type, $model_id, $type = null, Request $request ) {

		$roletype = $request->input( 'roletype' ) ?? 'Sale';
		$products = Product::latest()->paginate( Setting::get( 'pagination' ) );

		$doSelection = true;

		if ($type == 'images') {
			$attachImages = true;
		} else {
			$attachImages = false;
		}

		if (Order::find($model_id)) {
			$selected_products = self::getSelectedProducts($model_type,$model_id);
		} else {
			$selected_products = [];
		}

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
		                                        ->selected(1)
		                                        ->renderAsDropdown();


		return view( 'partials.grid', compact( 'products', 'roletype', 'model_id', 'selected_products', 'doSelection', 'model_type', 'search_suggestions', 'category_selection', 'attachImages' ) );
	}

	public function attachImages($model_type, $model_id = null, $status = null, $assigned_user = null, Request $request) {

		$roletype = $request->input( 'roletype' ) ?? 'Sale';
		$products = Product::latest();
		$filtered_category = '';
		$brand = '';
		$message_body = $request->message ? $request->message : '';

		if (Order::find($model_id)) {
			$selected_products = self::getSelectedProducts($model_type,$model_id);
		} else {
			$selected_products = [];
		}

		if ($request->brand != '') {
			$products = $products->where('brand', $request->brand);

			$brand = $request->brand;
		} else {
			if (Cache::has('filter-brand-' . Auth::id())) {
				$products = $products->where('brand', Cache::get('filter-brand-' . Auth::id()));

				$brand = Cache::get('filter-brand-' . Auth::id());
			}
		}

		$filtered_category = json_decode($request->category, true);

		if ($filtered_category[0] == null) {
			if (Cache::has('filter-category-' . Auth::id())) {
				$filtered_category[0] = Cache::get('filter-category-' . Auth::id());
			}
		}

		if ($filtered_category[0] != null) {
			$is_parent = Category::isParent($filtered_category[0]);
			$category_children = [];

			if ($is_parent) {
				$childs = Category::find($filtered_category[0])->childs()->get();

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
				array_push($category_children, $filtered_category);
			}

			$products = $products->whereIn('category', $category_children);
		}

		if (Cache::has('filter-color-' . Auth::id())) {
			$color = Cache::get('filter-color-' . Auth::id());
			$products = $products->where('color', $color);
		}

		if (Cache::has('filter-supplier-' . Auth::id())) {
			$supplier = Cache::get('filter-supplier-' . Auth::id());
			$products = $products->where('supplier', $supplier);
		}

		if ($request->page) {
			Cache::put('filter-page-' . Auth::id(), $request->page, 120);
		} else {
			if (Cache::has('filter-page-' . Auth::id())) {
				$page = Cache::get('filter-page-' . Auth::id());
				$request->request->add(['page' => $page]);
			}
		}

		$products = $products->select(['id', 'sku', 'size', 'price_special', 'supplier'])->paginate(Setting::get('pagination'));

		$category_selection = Category::attr(['name' => 'category[]','class' => 'form-control select-multiple'])
		                                        ->selected($filtered_category)
		                                        ->renderAsDropdown();

		if ($request->ajax()) {
			$html = view('partials.image-load', ['products' => $products, 'selected_products' => ($request->selected_products ? json_decode($request->selected_products) : [])])->render();

			return response()->json(['html' => $html]);
		}

		return view( 'partials.image-grid', compact( 'products', 'roletype', 'model_id', 'selected_products', 'model_type', 'status', 'assigned_user', 'category_selection', 'brand', 'filtered_category', 'color', 'supplier', 'message_body') );
	}


	public function attachProductToModel( $model_type ,$model_id, $product_id ) {

		switch ($model_type){
			case 'order':
			$action = OrderController::attachProduct($model_id,$product_id);

			break;

			case  'sale':
				$action = SaleController::attachProduct($model_id,$product_id);
			break;
			case 'stock':
				$stock = Stock::find($model_id);
				$product = Product::find($product_id);

				$stock->products()->attach($product);
				$action = 'Attached';
			break;
		}


		return [ 'msg' => 'success' , 'action' => $action ];
	}

	public static function getSelectedProducts($model_type,$model_id) {

		switch ( $model_type ) {
			case 'order':
				$order             = Order::findOrFail( $model_id );
				$selected_products = $order->order_product()->with( 'product' )->get()->pluck( 'product.id' )->toArray();
				break;

			case 'sale':
				$sale              = Sale::findOrFail( $model_id );
				$selected_products = json_decode( $sale->selected_product ,true) ?? [];
				break;

			default :
				$selected_products = [];
		}

		return $selected_products;
	}

	public function store(Request $request)
	{
		$this->validate($request, [
			'sku' => 'required|unique:products'
		]);

		$product = new Product;

		// return response()->json(['ok' => $request->file('image')->getClientOriginalExtension()]);

		$product->name = $request->name;
		$product->sku = $request->sku;
		$product->size = $request->size;
		$product->brand = $request->brand;
		$product->color = $request->color;
		$product->supplier = $request->supplier;
		$product->price = $request->price;

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

		$product->detachMediaTags(config('constants.media_tags'));
		$media = MediaUploader::fromSource($request->file('image'))->upload();
		$product->attachMedia($media,config('constants.media_tags'));

		$product_image = $product->getMedia(config('constants.media_tags'))->first() ? $product->getMedia(config('constants.media_tags'))->first()->getUrl() : '';

		if ($request->order_id) {
			$order_product = new OrderProduct;

			$order_product->order_id = $request->order_id;
			$order_product->sku = $request->sku;
			$order_product->product_price = $request->price_special;
			$order_product->size = $request->size;
			$order_product->color = $request->color;
			$order_product->qty = $request->quantity;

			$order_product->save();

			// return response($product);

			return response(['product' => $product, 'order' => $order_product, 'quantity' => $request->quantity, 'product_image' => $product_image]);
		} elseif ($request->stock_id) {
			$stock = Stock::find($request->stock_id);
			$stock->products()->attach($product);

			return response(['product' => $product, 'product_image' => $product_image]);
		}

		return redirect()->back()->with('success', 'You have successfully uploaded product!');
	}
}
