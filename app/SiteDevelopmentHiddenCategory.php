<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteDevelopmentHiddenCategory extends Model
{
    protected $fillable = [
        'category_id', 'store_website_id','created_at', 'updated_at'
    ];

    public function storeWebsite()
    {
        return $this->hasOne('\App\StoreWebsite','store_website_id','id');
    }
}
