<?php

namespace App\Marketing;

use Illuminate\Database\Eloquent\Model;
use App\Customer;
use App\ImQueue;

class WhatsappConfig extends Model
{
    protected $fillable = ['number', 'provider', 'username', 'password', 'is_customer_support'];

    public function customer()
    {
        return $this->hasMany(Customer::class, 'broadcast_number', 'number');
    }

    public function imQueueCurrentDateMessageSend()
    {
    	return $this->hasMany(ImQueue::class,'number_from','number');
    }
}
