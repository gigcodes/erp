<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteCategory extends Model
{
    protected $fillable = [
        'category_id', 'remote_id', 'store_website_id', 'created_at', 'updated_at', 'category_name'
    ];
}
