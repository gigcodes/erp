<?php

namespace App\UserEvent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserEvent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'subject',
        'description',
        'date',  // in case of a date only entry (time will be chosen by attendees)
        'start', // date time to determine the start of event
        'end',
        'daily_activity_id',
        'asset_manager_id',
    ];

    public function attendees()
    {
        return $this->hasMany(
            \App\UserEvent\UserEventAttendee::class
        );
    }

    public function user()
    {
        return $this->belongsTo(
            \App\User::class
        );
    }
}
