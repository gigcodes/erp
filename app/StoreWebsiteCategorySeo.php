<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteCategorySeo extends Model
{
    protected $fillable = [
        'category_id', 'meta_title', 'meta_description', 'meta_keyword', 'created_at', 'updated_at','language_id'
    ];
}
