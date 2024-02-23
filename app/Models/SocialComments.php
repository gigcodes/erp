<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class SocialComments extends Model
{
    protected $fillable = [
        'comment_ref_id',
        'commented_by_id',
        'commented_by_user',
        'post_id',
        'config_id',
        'message',
        'parent_id',
        'user_id',
        'created_at',
        'can_comment',
    ];

    protected $casts = [
        'can_comment' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
