<?php

namespace App\Http\Controllers;

use App\Colors;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Setting;
use App\Customer;
use Validator;

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

			$full_name = $results['billing_address']['firstname'] . ' ' . $results['billing_address']['lastname'];

			$customer_phone = str_replace(' ', '', $results['billing_address']['telephone']);
			$final_phone = '';

			if ($customer_phone != null) {
				if ($results['billing_address']['country_id'] == 'IN') {
					if (strlen($customer_phone) <= 10) {
						$customer_phone = '91' . $customer_phone;
					}
				}

				$customer = Customer::where('phone', $customer_phone)->first();
			} else {
				$customer = Customer::where('name', 'LIKE', "%$full_name%")->first();
			}

			if ($customer) {
				$customer_id = $customer->id;

				if ($customer_phone != null) {
					$final_phone = $customer_phone;
				}
			} else {
				$customer = new Customer;
				$customer->name = $full_name;
				$customer->address = $results['billing_address']['street'];
				$customer->city = $results['billing_address']['city'];
				$customer->country = $results['billing_address']['country_id'];
				$temp_number = [];

				if ($customer_phone != null) {
					$temp_number['phone'] = $customer_phone;
				} else {
					$temp_number['phone'] = self::generateRandomString();
				}

				$final_phone = self::validatePhone($temp_number);
				$customer->phone = $final_phone;

				$customer->save();

				$customer_id = $customer->id;
			}

			$id             = DB::table( 'orders' )->insertGetId(
				array(
					'customer_id'    => $customer_id,
					'order_id'       => $results['increment_id'],
					'order_type'     => 'online',
					'order_date'     => $results['created_at'],
					'client_name'    => $results['billing_address']['firstname'] . ' ' . $results['billing_address']['lastname'],
					'city'           => $results['billing_address']['city'],
					'advance_detail' => $paid,
					'contact_detail' => $final_phone,
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

	public static function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
	}

	public static function validatePhone($phone) {
		$validator = Validator::make($phone, [
			'phone' => 'unique:customers,phone'
		]);

		if ($validator->fails()) {
			$phone['phone'] = self::generateRandomString();

			self::validatePhone($phone);
		}

		return $phone['phone'];
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
