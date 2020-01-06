<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\InstagramPosts;

class InstagramCommentQueue extends Model
{
    public function getPost()
    {
    	return $this->hasOne(InstagramPosts::class,'id','post_id');
    }
}
