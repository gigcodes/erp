<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListingPayments extends Model
{
    protected $casts = [
        'product_ids' => 'array'
    ];
}
