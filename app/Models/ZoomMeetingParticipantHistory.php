<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class ZoomMeetingParticipantHistory extends Model
{
    use HasFactory;

    protected $table = 'zoom_meeitng_participants_histories';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
