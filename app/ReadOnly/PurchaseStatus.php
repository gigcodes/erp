<?php
/**
 * Created by PhpStorm.
 * User: mustafaflexwala
 * Date: 13/10/18
 * Time: 7:12 PM
 */

namespace App\ReadOnly;


use App\ReadOnlyBase;

class PurchaseStatus extends ReadOnlyBase {

	protected $data = [
		'Pending Purchase'	=> 'Pending Purchase',
		'Payment to be made to supplier'	=> 'Payment to be made to supplier',
		'Purchased'	=> 'Purchased',
		'Follow up for shipment'	=> 'Follow up for shipment',
		'Ordered' => 'Ordered',
		'Shipped' => 'Shipped',
		'Delivered' => 'Delivered',
		'Canceled' => 'Canceled',
		'In transit in Italy' => 'In transit in Italy',
		'In transit in Dubai' => 'In transit in Dubai',
		'Received in Mumbai' => 'Received in Mumbai',
	];
}
