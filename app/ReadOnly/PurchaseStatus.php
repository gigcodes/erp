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
		'Proceed without Advance' => 'Proceed without Advance',
		'Advance received' => 'Advance received',
		'Cancel' => 'Cancel',
		'TEST' => 'TEST',
	];
}
