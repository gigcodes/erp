<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class ZoomMeetingRecordHistory extends Model
{
    use HasFactory;

    protected $table = 'zoom_meeting_recordings_histories';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
