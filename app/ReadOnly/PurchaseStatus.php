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
		'Ordered' => 'Ordered',
		'Shipped' => 'Shipped',
		'In transit in Italy' => 'In transit in Italy',
		'In transit in Dubai' => 'In transit in Dubai',
		'Received in Mumbai' => 'Received in Mumbai',
	];
}
