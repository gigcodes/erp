<?php

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class SuggestedProduct extends Model
{
          /**
     * @var string
    
      * @SWG\Property(property="brands",type="string")
      * @SWG\Property(property="categories",type="string")
      * @SWG\Property(property="keyword",type="string")
      * @SWG\Property(property="color",type="string")
      * @SWG\Property(property="supplier",type="string")
      * @SWG\Property(property="location",type="string")
      * @SWG\Property(property="size",type="string")
      * @SWG\Property(property="customer_id",type="integer")
     * @SWG\Property(property="total",type="float")
     */
    protected $fillable = [
        'brands',
        'categories',
        'keyword',
        'color',
        'supplier',
        'location',
        'size',
        'customer_id',
        'total'
    ];

    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }
}
