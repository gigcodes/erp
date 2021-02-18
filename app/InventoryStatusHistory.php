<?php

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class InventoryStatusHistory extends Model
{
	  /**
     * @var string
    
     * @SWG\Property(property="in_stock",type="boolean")
     * @SWG\Property(property="product_id",type="integer")
      * @SWG\Property(property="date",type="datetime")
     * @SWG\Property(property="prev_in_stock",type="integer")
     * @SWG\Property(property="supplier_id",type="integer")

     */
    protected $fillable = ['product_id','date','in_stock','prev_in_stock','supplier_id'];

    public static function getInventoryHistoryFromProductId($product_id)
    {

        $columns = array('in_stock','prev_in_stock','date','supplier_id');

        return \App\InventoryStatusHistory::where('product_id',$product_id)->get($columns);
    }
}
