<?php

namespace App\Marketing;

use Illuminate\Database\Eloquent\Model;
use App\Customer;
use App\ImQueue;
use Carbon\Carbon;

class WhatsappConfig extends Model
{
    protected $fillable = ['number', 'provider', 'username', 'password', 'is_customer_support','frequency','send_start','send_end'];

    public function customer()
    {
        return $this->hasMany(Customer::class, 'broadcast_number', 'number');
    }

    public function imQueueCurrentDateMessageSend()
    {
    	return $this->hasMany(ImQueue::class,'number_from','number')->whereDate('created_at', Carbon::today());
    }

    public function  imQueueLastMessageSend()
    {
    	return $this->hasOne(ImQueue::class,'number_from','number')->latest();
    }

    public function imQueueLastMessagePending()
    {
        return $this->hasMany(ImQueue::class,'number_from','number')->whereNull('sent_at');
    }
}
