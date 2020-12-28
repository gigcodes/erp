<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsitePageHistory extends Model
{
    protected $fillable = [
        'content', 'store_website_page_id', 'updated_by', 'created_at', 'updated_at',
    ];
}
