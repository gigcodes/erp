<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteProductAttribute extends Model
{
    protected $fillable = [
        'product_id', 'description', 'store_website_id', 'created_at', 'updated_at',
    ];
}
