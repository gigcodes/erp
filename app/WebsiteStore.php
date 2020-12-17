<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class WebsiteStore extends Model
{
    protected $fillable = [
        'name', 
        'code', 
        'country_code', 
        'root_category', 
        'platform_id', 
        'website_id' 
    ];

    public function website()
    {
        return $this->hasOne(\App\Website::class, 'id','website_id');
    }
}
