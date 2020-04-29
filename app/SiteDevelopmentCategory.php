<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SiteDevelopment;

class SiteDevelopmentCategory extends Model
{
    protected $fillable = ['title'];


    public function development()
    {
    	return $this->hasOne(SiteDevelopment::class,'site_development_category_id','id');
    }

    public function getDevelopment($categoryId,$websiteId)
    {
    	return SiteDevelopment::where('website_id',$websiteId)->where('site_development_category_id',$categoryId)->first();
    	
    }
}
