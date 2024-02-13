<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialComments extends Model
{
    protected $fillable = [
        'comment_ref_id',
        'post_id',
        'config_id',
        'message',
        'parent_id',
        'user_id',
    ];
}
