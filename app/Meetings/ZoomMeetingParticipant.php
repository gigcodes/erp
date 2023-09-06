<?php

namespace App\Meetings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ZoomMeetingParticipant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'zoom_meeting_participants';

    protected $fillable = ['meeting_id', 'name', 'email'];
}
