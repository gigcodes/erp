<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Customer;

class CustomerLiveChat extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['customer_id','thread','status','seen'];


    public function customer(){
        return $this->hasOne(Customer::class,'customer_id','id');
    }
}
