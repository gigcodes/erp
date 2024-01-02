<?php

namespace App\Loggers;

use Illuminate\Database\Eloquent\Model;

class TimeDoctorCommandLogMessage extends Model
{
    protected $guarded = [];

    public function timeDoctorCommandLog()
    {
        return $this->belongsTo(\App\Loggers\TimeDoctorCommandLog::class);
    }
}
