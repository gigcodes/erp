<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{

	use LogsActivity;
	use Mediable;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'sku'
	];

	protected static $logName = 'Product';

	protected static $logAttributes = ['sku'];

	/*public function getDescriptionForEvent(string $eventName): string
	{
		return "This model has been {$eventName}";
	}*/

	public function notifications(){
		return $this->hasMany('App\Notification');
	}
}
