<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ErpLeads extends Model
{
    //
    //use SoftDeletes;

    protected $fillable = [
        'lead_status_id',
        'customer_id',
        'product_id',
        'brand_id',
        'category_id',
        'color',
        'size',
        'min_price',
        'max_price',
        'created_at',
        'updated_at',
    ];
}
