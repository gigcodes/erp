<?php

namespace App\Http\Controllers;

use App\Colors;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Setting;

class MagentoController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store( Request $request ) {
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show( $id ) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit( $id ) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update( Request $request, $id ) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy( $id ) {
		//
	}

	public static function get_magento_orders() {
		$options   = array(
			'trace'              => true,
			'connection_timeout' => 120,
			'wsdl_cache'         => WSDL_CACHE_NONE,
		);
		$proxy     = new \SoapClient( config( 'magentoapi.url' ), $options );
		$sessionId = $proxy->login( config( 'magentoapi.user' ), config( 'magentoapi.password' ) );
		$lastid    = Setting::get( 'lastid' );
		$filter    = array(
			'complex_filter' => array(
				array(
					'key'   => 'order_id',
					'value' => array( 'key' => 'gt', 'value' => $lastid )
				)
			)
		);
		$orderlist = $proxy->salesOrderList( $sessionId, $filter );

		for ( $j = 0; $j < sizeof( $orderlist ); $j ++ ) {


			$results = json_decode( json_encode( $proxy->salesOrderInfo( $sessionId, $orderlist[ $j ]->increment_id ) ), true );


			$atts = unserialize( $results['items'][0]['product_options'] );
			if ( ! empty( $results['total_paid'] ) ) {
				$paid = $results['total_paid'];
			} else {
				$paid = 0;
			}
			$balance_amount = $results['base_grand_total'] - $paid;
			$id             = DB::table( 'orders' )->insertGetId(
				array(
					'order_id'       => $results['increment_id'],
					'order_type'     => 'online',
					'order_date'     => $results['created_at'],
					'client_name'    => $results['billing_address']['firstname'] . ' ' . $results['billing_address']['lastname'],
					'city'           => $results['billing_address']['city'],
					'advance_detail' => $paid,
					'contact_detail' => $results['billing_address']['telephone'],
					'balance_amount' => $balance_amount,
					'created_at'     => $results['created_at'],
					'updated_at'     => $results['created_at'],
				) );

			$noproducts = sizeof( $results['items'] );
			for ( $i = 0; $i < $noproducts; $i ++ ) {


				if ( round( $results['items'][ $i ]['price'] ) > 0 ) {

					if ( $results['items'][ $i ]['product_type'] == 'configurable' && !empty($atts['attributes_info'][0]['label']) ) {
						if ( $atts['attributes_info'][0]['label'] == 'Sizes' ) {
							$size = $atts['attributes_info'][0]['value'];
						}
					} else {
						$size = '';
					}
					$skuAndColor = self::getSkuAndColor( $results['items'][ $i ]['sku'] );

					DB::table( 'order_products' )->insert(
						array(
							'order_id'      => $id,
							'sku'           => $skuAndColor['sku'],
							'product_price' => round( $results['items'][ $i ]['price'] ),
							'qty'           => round( $results['items'][ $i ]['qty_ordered'] ),
							'size'          => $size,
							'color'         => $skuAndColor['color'],
							'created_at'    => $results['created_at'],
							'updated_at'    => $results['created_at'],
						) );
				}
			}
			Setting::add( 'lastid', $orderlist[ $j ]->order_id, 'int' );
		}

	}


	static function getSkuAndColor( $original_sku ) {

		$result = [];
		$colors = ( new Colors() )->all();

		$splitted_sku = explode( '-', $original_sku );

		foreach ( $colors as $color ) {

			if ( strpos( $splitted_sku[0], $color ) ) {

				$result['color'] = $color;
				$result['sku']   = str_replace( $color, '', $splitted_sku[0] );

				return $result;
			}
		}

		$result['color'] = null;
		$result['sku']   = $splitted_sku[0];

		return $result;
	}
}
