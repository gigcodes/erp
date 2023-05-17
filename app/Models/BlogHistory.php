<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogHistory extends Model
{
    public $fillable = [
        'blog_id',
        'plaglarism',
        'internal_link',
        'external_link',
        'user_id',
        'create_time',
        'no_index',
        'no_follow',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
