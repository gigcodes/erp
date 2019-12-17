<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\InstagramPosts;

class HashTag extends Model
{
    protected $fillable = ['hashtag'];

    public function posts()
    {
        return $this->hasMany(HashtagPosts::class, 'hashtag_id', 'id');
    }

    public function instagramPost()
    {
        return $this->hasMany(InstagramPosts::class, 'hashtag_id', 'id');
    }

}
