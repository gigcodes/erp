<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class ProductDispatch extends Model
{
    use Mediable;
    
	public $table  = "product_dispatch";
    protected $fillable = ['modeof_shipment','awb','eta','date_time','product_id','created_by'];

    public function product()
    {
    	return $this->hasOne("\App\Products","id","product_id");
    }

    public function user()
    {
    	return $this->hasOne("\App\User","id","created_by");
    }
}
