<?php

namespace App\Loggers;

use Illuminate\Database\Eloquent\Model;

class TimeDoctorCommandLog extends Model
{
    public function messages()
    {
        return $this->hasMany(\App\Loggers\TimeDoctorCommandLogMessage::class);
    }
}
