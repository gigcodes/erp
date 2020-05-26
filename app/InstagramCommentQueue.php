<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\InstagramPosts;
use App\Account;

class InstagramCommentQueue extends Model
{
    public function getPost()
    {
    	return $this->hasOne(InstagramPosts::class,'id','post_id');
    }

    public function account()
    {
    	return $this->hasOne(Account::class,'id','account_id');
    }


}
