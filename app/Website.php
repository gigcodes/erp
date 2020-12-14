<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Website extends Model
{
    protected $fillable = [
        'name', 
        'code', 
        'sort_order', 
        'platform_id', 
        'store_website_id', 
    ];

    public function stores()
    {
        return $this->hasMany(App\WebsiteStore::class, 'website_id', 'id');
    }
}
