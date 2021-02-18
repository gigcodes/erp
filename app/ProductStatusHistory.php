<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductStatusHistory extends Model
{
    public $table = 'product_status_histories';

    public static function getStatusHistoryFromProductId($product_id)
    {

        $columns = array('old_status','new_status','created_at');

        return \App\ProductStatusHistory::where('product_id',$product_id)->get($columns);
    }

    public static function addStatusToProduct($data)
    {
        \App\ProductStatusHistory::insert($data);
    }

    public function product(){
        return $this->belongsTo('App\Product');
    }
}
