<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerAddressData extends Model
{
    //
    protected $fillable = [
        'customer_id', 'address_1', 'address_2', 'address_3', 'country', 'city', 'state', 'postcode'
      ];
}
