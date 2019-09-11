<?php

namespace App;
use App\LinksToPost;

use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    protected $fillable = array('name');

    public function listToPostTo()
    {
     return $this->belongsTo(LinksToPost::class,'id','category_id');
    }
}