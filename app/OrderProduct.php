<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{

    protected $fillable = [
		'order_id',
	    'sku',
	    'product_price',
	    'size',
	    'color',
	    'qty',
    ];

	public function order(){

		return $this->belongsTo('App\Order','order_id','id');
	}

	public function product(){

//		return $this->hasOne('App\Product',['sku','color'],['sku','color']);
		return $this->hasOne('App\Product','sku','sku');

	}
}
