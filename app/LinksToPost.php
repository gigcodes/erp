<?php

namespace App;
use App\ArticleCategory;

use Illuminate\Database\Eloquent\Model;

class LinksToPost extends Model
{
    protected $fillable = ['link','name','category_id','date_scrapped','date_posted','date_next_post','article_posted'];

    public function articleCategory()
    {
    	return $this->hasOne(ArticleCategory::class);
    }
}