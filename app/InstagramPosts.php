<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class InstagramPosts extends Model
{

    use Mediable;

    protected $casts = [
        'media_url' => 'array'
    ];

    public function comments() {
        return $this->hasMany(InstagramPostsComments::class, 'instagram_post_id', 'id');
    }
}
