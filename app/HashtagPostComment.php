<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HashtagPostComment extends Model
{
    public function post() {
        return $this->belongsTo(HashtagPosts::class, 'hashtag_post_id', 'id');
    }

}
