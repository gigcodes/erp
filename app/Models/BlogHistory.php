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
        'create_time',
        'no_index',
        'no_follow',
        'created_at',
        'updated_at',
    ];
}
