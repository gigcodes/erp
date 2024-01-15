<?php

namespace App\Models;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFeedbackRemark extends Model
{
    use HasFactory;

    public $fillable = [
        'user_feedback_category_id',
        'user_feedback_vendor_id',
        'remarks',
        'added_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}