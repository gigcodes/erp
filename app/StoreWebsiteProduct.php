<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteProduct extends Model
{
    //

    protected $fillable = [
        'store_website_id',
        'product_id',
        'platform_id'
    ];

}
