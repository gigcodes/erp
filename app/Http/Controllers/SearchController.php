<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\Sale;
use App\Setting;
use App\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller {
	public function __construct() {
		$this->middleware( 'permission:product-list' );
	}

	public function search( Stage $stage, Request $request ) {

		$data     = [];
		$term     = $request->input( 'term' );
		$roletype = $request->input( 'roletype' );

		$data['term']     = $term;
		$data['roletype'] = $roletype;

		$doSelection = $request->input( 'doSelection' );

		if ( ! empty( $doSelection ) ) {

			$data['doSelection'] = true;
			$data['model_id']    = $request->input( 'model_id' );
			$data['model_type']  = $request->input( 'model_type' );

			$data['selected_products'] = ProductController::getSelectedProducts($data['model_type'],$data['model_id']);
		}


		$productQuery = ( new Product() )->newQuery()
		                                 ->latest()
		                                 ->orWhere( 'sku', $term )
		                                 ->orWhere( 'id', $term )//		                                 ->orWhere( 'category', $term )
		;


		if ( $term == - 1 ) {

			$productQuery = $productQuery->orWhere( 'isApproved', - 1 );
		}

		if ( BrandController::getBrandIds( $term ) ) {

			$productQuery = $productQuery->orWhere( 'brand', BrandController::getBrandIds( $term ) );
		}

		if ( CategoryController::getCategoryIdByName( $term ) ) {

			$productQuery = $productQuery->orWhere( 'category', CategoryController::getCategoryIdByName( $term ) );
		}

		if ( ! empty( $stage->getIDCaseInsensitive( $term ) ) ) {

			$productQuery = $productQuery->orWhere( 'stage', $stage->getIDCaseInsensitive( $term ) );
		}

		if ( ! ( \Auth::user()->hasRole( [ 'Admin', 'Supervisors' ] ) ) ) {

			$productQuery = $productQuery->where( 'stage', '>=', $stage->get( $roletype ) );
		}

		if ( $roletype != 'Selection' && $roletype != 'Searcher' ) {

			$productQuery = $productQuery->whereNull( 'dnf' );
		}


		$data['products'] = $productQuery->paginate( Setting::get( 'pagination' ) );

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

		return view( 'partials.grid', compact( 'products', 'roletype' ) );
	}
}
