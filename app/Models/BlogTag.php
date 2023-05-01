<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model
{
    public $fillable = [
        'blog_id',
        'type',
        'tag_id',
        'created_at',
        'updated_at',
    ];

    public function tag()
    {
        return $this->belongsTo(\App\Tag::class);
    }
}
