<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFeedbackCategorySopHistoryComment extends Model
{
    protected $fillable = [
        'id', 'user_id', 'sop_history_id', 'comment', 'created_at', 'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
