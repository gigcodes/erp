<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierPriceRange extends Model
{
    protected $table = 'supplier_price_ranges';
    public $timestamps = false;
    protected $fillable = [
        'price_from',
		'price_to',
    ];
}