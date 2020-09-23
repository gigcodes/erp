<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerOrderCharities extends Model
{
    //
	protected $fillable = ['customer_id', 'order_id', 'charity_id', 'amount'];
	
	public function user()
    {
        return $this->belongsTo('App\Customer');
    }
}
