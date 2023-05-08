<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleScreencast extends Model
{
    protected $table = 'google_drive_screencast_upload';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
