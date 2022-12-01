<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFeedbackCategorySopHistory extends Model
{
    protected $fillable = [
        'id', 'user_id', 'category_id', 'sop', 'created_at', 'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
