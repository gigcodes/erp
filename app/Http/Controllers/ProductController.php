<?php


namespace App\Http\Controllers;


use App\Category;
use App\Order;
use App\Product;
use App\Sale;
use App\Setting;
use App\Sizes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;


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

	public function attachProducts( $model_type, $model_id, Request $request ) {

		$roletype = $request->input( 'roletype' ) ?? 'Sale';
		$products = Product::latest()->paginate( Setting::get( 'pagination' ) );

		$doSelection = true;

		$selected_products = self::getSelectedProducts($model_type,$model_id);


		return view( 'partials.grid', compact( 'products', 'roletype', 'model_id', 'selected_products', 'doSelection', 'model_type' ) );
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
}