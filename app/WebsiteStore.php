<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class WebsiteStore extends Model
{
    protected $fillable = [
        'name', 
        'code', 
        'root_category', 
        'platform_id', 
        'website_id' 
    ];

    public function website()
    {
        return $this->hasOne(\App\Website::class, 'id','website_id');
    }

    public function storeView()
    {
        return $this->hasMany(\App\WebsiteStoreView::class, 'website_store_id','id');
    }
	
	 public function scrapperImage()
    {
        return $this->hasMany(\App\scraperImags::class, 'website_id', 'code');
    }
}
