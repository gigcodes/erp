<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ZoomMeetingHistory extends Model
{
    use HasFactory;

    protected $table = 'zoom_meeting_histories';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
