<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteSize extends Model
{
    //
    protected $fillable = ['size_id', 'store_website_id'];

    public function storeWebsite()
    {
        return $this->hasOne(\App\StoreWebsite::class,'id','store_website_id');
    }
}
