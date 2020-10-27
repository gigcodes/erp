<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryStatusHistory extends Model
{
    protected $fillable = ['product_id','date','in_stock','prev_in_stock','supplier_id'];

    public static function getInventoryHistoryFromProductId($product_id)
    {

        $columns = array('in_stock','prev_in_stock','date','supplier_id');

        return \App\InventoryStatusHistory::where('product_id',$product_id)->get($columns);
    }
}
