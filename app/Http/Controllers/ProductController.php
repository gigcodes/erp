<?php


namespace App\Http\Controllers;


use App\Category;
use App\Order;
use App\OrderProduct;
use App\Product;
use App\Sale;
use App\Setting;
use App\Sizes;
use App\Brand;
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
	public function index() {
		$products = Product::latest()->paginate( Setting::get( 'pagination' ) );

		return view( 'products.index', compact( 'products' ) )
			->with( 'i', ( request()->input( 'page', 1 ) - 1 ) * 10 );
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
		$data['supplier_link']    = $product->supplier_link;
		$data['description_link'] = $product->description_link;

		$data['images'] = $product->getMedia( config( 'constants.media_tags' ) );

		$data['categories'] = $product->category ? CategoryController::getCategoryTree( $product->category ) : '';

		return view( 'partials.show', $data );
	}

	public function destroy( Product $product ) {
		$product->delete();

		return redirect()->route( 'products.index' )
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

	public function attachImages($model_type, $model_id, $status, $assigned_user, Request $request) {

		$roletype = $request->input( 'roletype' ) ?? 'Sale';
		$products = Product::latest()->paginate( Setting::get( 'pagination' ) );

		// $doSelection = true;
		//
		// if ($type == 'images') {
		// 	$attachImages = true;
		// } else {
		// 	$attachImages = false;
		// }

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

		if ($request->ajax()) {
			return view('partials.image-load', ['products' => $products])->render();
		}

		return view( 'partials.image-grid', compact( 'products', 'roletype', 'model_id', 'selected_products', 'model_type', 'status', 'assigned_user', 'search_suggestions', 'category_selection') );
	}


	public function attachProductToModel( $model_type ,$model_id, $product_id ) {

		switch ($model_type){
			case 'order':
			$action = OrderController::attachProduct($model_id,$product_id);

			break;

			case  'sale':
			$action = SaleController::attachProduct($model_id,$product_id);
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
		$product->price = $request->price;
		$product->brand = $request->brand;
		$product->color = $request->color;

		$product->save();

		$media = MediaUploader::fromSource($request->file('image'))->upload();
		$product->attachMedia($media,config('constants.media_tags'));

		$order_product = new OrderProduct;

		$order_product->order_id = $request->order_id;
		$order_product->sku = $request->sku;
		$order_product->product_price = $request->price;
		$order_product->size = $request->size;
		$order_product->color = $request->color;
		$order_product->qty = $request->quantity;

		$order_product->save();

		// return response($product);

		return response(['product' => $product, 'order' => $order_product, 'quantity' => $request->quantity]);
	}
}
