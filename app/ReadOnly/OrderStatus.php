<?php
/**
 * Created by PhpStorm.
 * User: mustafaflexwala
 * Date: 13/10/18
 * Time: 7:12 PM
 */

namespace App\ReadOnly;


use App\ReadOnlyBase;

class OrderStatus extends ReadOnlyBase {

	protected $data = [
//		'Order' => 'Order',
//		'Advance' => 'Advance',
		'Proceed without Advance' => 'Proceed without Advance',
		'Advance received' => 'Advance received',
		'Cancel' => 'Cancel',
		'Prepaid' => 'Prepaid',
		'Product Shiped form Italy' => 'Product Shiped form Italy',
		'In Transist from Italy' => 'In Transist from Italy',
		'Product shiped to Client' => 'Product shiped to Client',
		'Delivered' => 'Delivered',
		'Refund to be processed' => 'Refund to be processed',
	];
}