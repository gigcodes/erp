<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScrapInfluencer extends Model
{
    protected $fillable = [
        'post_id',
        'post_caption',
        'instagram_user_id',
        'post_media_type',
        'post_code',
        'post_location',
        'post_hashtag_id',
        'post_likes',
        'post_comments_count',
        'post_media_url',
        'posted_at',
        'comment_user_id',
        'comment_user_full_name',
        'comment_username',
        'instagram_post_id',
        'comment_id',
        'comment',
        'comment_profile_pic_url',
        'comment_posted_at'
    ];
}
