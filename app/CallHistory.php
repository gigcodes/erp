<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CallHistory extends Model
{
	/**
     * @var string
     * @SWG\Property(enum={"customer_id", "status"})
     */
  protected $fillable = ['customer_id', 'status'];

  public function customer() {
    return $this->belongsTo('App\Customer');
  }
  public function store_website(){
      return $this->belongsTo(StoreWebsite::class);
  }
}
