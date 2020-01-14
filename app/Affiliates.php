<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\HashTag;

class Affiliates extends Model
{
    protected $fillable = ['location'];

    public function hashTags()
    {
        return $this->belongsTo(HashTag::class, 'hashtag_id');
    }
}
