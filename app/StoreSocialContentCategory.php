<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreSocialContentCategory extends Model
{
    protected $fillable = ['title'];

    public function getContent($categoryId,$websiteId)
    {
    	return StoreSocialContent::where('store_website_id',$websiteId)->where('store_social_content_category_id',$categoryId)->first();
    	
    }
}
