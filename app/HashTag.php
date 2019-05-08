<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HashTag extends Model
{
    public function posts() {
        return $this->hasMany(HashtagPosts::class, 'hashtag_id', 'id');
    }

}
