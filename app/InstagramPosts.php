<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstagramPosts extends Model
{
    protected $casts = [
        'media_url' => 'array'
    ];

    public function comments() {
        return $this->hasMany(InstagramPostsComments::class, 'instagram_post_id', 'id');
    }
}
