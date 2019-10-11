<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductLocationHistory extends Model
{
	public $table  = "product_location_history";
    protected $fillable = ['location_name','courier_name','courier_details','date_time','product_id','created_by'];

    public function product()
    {
    	return $this->hasOne("\App\Products","id","product_id");
    }

    public function user()
    {
    	return $this->hasOne("\App\User","id","created_by");
    }
}
