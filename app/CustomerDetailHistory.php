<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerDetailHistory extends Model
{
	protected $table = "customer_detail_history";
    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'pincode'
        
    ];
}
