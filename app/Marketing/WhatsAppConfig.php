<?php

namespace App\Marketing;

use Illuminate\Database\Eloquent\Model;
use App\Customer;

class WhatsappConfig extends Model
{
    protected $fillable = ['number', 'provider', 'username', 'password', 'is_customer_support'];

    public function customer()
    {
        return $this->hasMany(Customer::class, 'whatsapp_number', 'number');
    }
}
