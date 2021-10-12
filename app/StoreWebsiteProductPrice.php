<?php

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class StoreWebsiteProductPrice extends Model
{
	/**
     * @var string

     * @SWG\Property(property="name",type="string")
     */

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
    public function store_website()
    {
        return $this->belongsTo('App\StoreWebsite');
    }
   
}
