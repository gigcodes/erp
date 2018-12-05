<?php
/**
 * Created by PhpStorm.
 * User: mustafaflexwala
 * Date: 17/08/18
 * Time: 9:57 PM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model {

	use SoftDeletes;

	protected $fillable = ['name','euro_to_inr','deduction_percentage','magento_id'];
	protected $dates = ['deleted_at'];

	public static function getAll(){

		$brands  = self::all();

		$brandsArray = [];

		foreach ($brands as $brand)
			$brandsArray[$brand->id] = $brand->name;

		return $brandsArray;
	}

}