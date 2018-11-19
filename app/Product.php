<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\Mediable;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{

//	use LogsActivity;
	use Mediable;
	use SoftDeletes;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'sku'
	];
	protected $dates = ['deleted_at'];

//	protected static $logName = 'Product';
//	protected static $logAttributes = ['sku'];

	public function notifications(){
		return $this->hasMany('App\Notification');
	}


	public function brands(){
		return $this->hasOne('App\Brand','id','brand');
	}

	public static function getPendingProductsCount($roleType){

		$stage = new Stage();
		$stage_no = intval($stage->getID($roleType));

		return DB::table('products')
		         ->where('stage',$stage_no-1)
				 ->where('isApproved','!=',-1)
				 ->whereNull('dnf')
		         ->whereNull('deleted_at')
		         ->count();
	}
}
