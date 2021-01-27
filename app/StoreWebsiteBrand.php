<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteBrand extends Model
{
    protected $fillable = [
        'brand_id', 'markup', 'store_website_id', 'created_at', 'updated_at', 'magento_value'
    ];
}
