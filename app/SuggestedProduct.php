<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SuggestedProduct extends Model
{
    protected $fillable = [
        'brands',
        'categories',
        'keyword',
        'color',
        'supplier',
        'location',
        'size',
        'customer_id',
        'total'
    ];

    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }
}
