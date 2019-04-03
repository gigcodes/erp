<?php

namespace App\Http\Controllers;

use App\Product;
use App\Setting;
use App\Stage;
use App\Brand;
use App\Category;
use Illuminate\Http\Request;

class ProductApproverController extends Controller
{
	public function __construct() {

		$this->middleware('permission:approver-list',['only' => ['index']]);
		$this->middleware('permission:approver-edit',['only' => ['edit','isFinal']]);
	}


	public function index(Stage $stage){

		$products = Product::latest()
		                   ->where('stage','>=',$stage->get('Lister'))
		                   ->whereNull('dnf')
											 ->select(['id', 'sku', 'size', 'price_special', 'brand', 'isApproved', 'stage', 'status', 'is_scraped', 'created_at'])
		                   ->paginate(Setting::get('pagination'));

		$roletype = 'Approver';

		$category_selection = Category::attr(['name' => 'category[]','class' => 'form-control select-multiple'])
		                                        ->selected(1)
		                                        ->renderAsDropdown();

		return view('partials.grid',compact('products','roletype', 'category_selection'))
			->with('i', (request()->input('page', 1) - 1) * 10);

	}

	public function edit(Product $productlister){

		return redirect( route('products.show',$productlister->id) );
	}

	public function isFinal(Product $product,Stage $stage){

		$result = self::magentoSoapUpdateStatus($product);

		if($result) {

			$product->isFinal = 1;
			$product->stage   = $stage->get( 'Approver' );
			$product->save();

			NotificaitonContoller::store( 'has Final Approved', [ 'Inventory' ], $product->id );
			ActivityConroller::create( $product->id, 'approver', 'create' );

			$next_product = Product::latest()
			                   ->where('stage','>=', '6')
			                   ->whereNull('dnf')->where('isFinal', '!=', '1')->first();

			return redirect()->route('products.show', $next_product->id)->with( 'success', 'Product has been Final Approved' );
		}

		return back()->with('error','Error Occured while uploading');

//		return ['msg'=>'success', 'isApproved'  => $product->isApproved ];
	}


	public function magentoSoapUpdateStatus($product){

		$options = array(
			'trace' => true,
			'connection_timeout' => 120,
			'wsdl_cache' => WSDL_CACHE_NONE,
		);
		$proxy = new \SoapClient(config('magentoapi.url'), $options);
		$sessionId = $proxy->login(config('magentoapi.user'), config('magentoapi.password'));

		$sku = $product->sku . $product->color;

//		$result = $proxy->catalogProductUpdate($sessionId, $sku , array('visibility' => 4));

		if(!empty($product->size)){

			$sizes_array = explode( ',', $product->size );

			foreach ($sizes_array as $size)
				$result = $proxy->catalogProductUpdate($sessionId, $sku . '-' . $size , array('status' => 1));

			$result = $proxy->catalogProductUpdate($sessionId, $sku , array('status' => 1));
		}
		else {
			$result = $proxy->catalogProductUpdate($sessionId, $sku , array('status' => 1));
		}

		return $result;
	}
}
