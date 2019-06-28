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
	protected $appends = [];
	protected $communication = '';
	protected $image_url = '';

	public function messages()
	{
		return $this->hasMany('App\Message', 'moduleid')->where('moduletype', 'product')->latest()->first();
	}

	public function product_category()
	{
		return $this->belongsTo('App\Category', 'category');
	}

	public function getCommunicationAttribute()
	{
		return $this->messages();
	}

	public function getImageurlAttribute()
	{
		return $this->getMedia(config('constants.media_tags'))->first() ? $this->getMedia(config('constants.media_tags'))->first()->getUrl() : '';
	}

//	protected static $logName = 'Product';
//	protected static $logAttributes = ['sku'];

	public function notifications(){
		return $this->hasMany('App\Notification');
	}

	public function suppliers()
	{
		return $this->belongsToMany('App\Supplier', 'product_suppliers', 'product_id', 'supplier_id');
	}

	public function suppliers_info()
	{
		return $this->hasMany('App\ProductSupplier');
	}

	public function private_views()
  {
    return $this->belongsToMany('App\PrivateView', 'private_view_products', 'product_id', 'private_view_id');
  }

	public function suggestions()
  {
    return $this->belongsToMany('App\Suggestion', 'suggestion_products', 'product_id', 'suggestion_id');
  }

  public function amends() {
	    return $this->hasMany(CropAmends::class, 'product_id', 'id');
  }

	public function brands(){
		return $this->hasOne('App\Brand','id','brand');
	}

	public function references(){
		return $this->hasMany('App\ProductReference');
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

	public function purchases()
  {
    return $this->belongsToMany('App\Purchase', 'purchase_products', 'product_id', 'purchase_id');
  }

	public function orderproducts()
  {
    return $this->hasMany('App\OrderProduct', 'sku', 'sku');
  }

	public function scraped_products()
	{
		return $this->hasOne('App\ScrapedProducts', 'sku', 'sku');
	}

	public function many_scraped_products()
	{
		return $this->hasMany('App\ScrapedProducts', 'sku', 'sku');
	}

	public function user()
	{
		return $this->belongsToMany('App\User', 'user_products', 'product_id', 'user_id');
	}

	public function cropApprover() {
	    return $this->belongsTo(User::class, 'crop_approved_by', 'id');
    }

    public function cropRejector() {
        return $this->belongsTo(User::class, 'crop_rejected_by', 'id');
    }

    public function approver() {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
    public function cropOrderer() {
        return $this->belongsTo(User::class, 'crop_ordered_by', 'id');
    }

    public function rejectedCropApprover() {
        return $this->hasOne(User::class, 'reject_approved_by', 'id');
    }
}
