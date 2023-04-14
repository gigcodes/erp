<?php

namespace App\TimeDoctor;

use Illuminate\Database\Eloquent\Model;

class TimeDoctorLog extends Model
{
    protected $fillable = [
        'time_doctor_account_id', 'url', 'payload', 'response', 'user_id', 'response_code',
    ];
}
