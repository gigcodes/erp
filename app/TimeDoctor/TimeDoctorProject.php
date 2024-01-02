<?php

namespace App\TimeDoctor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeDoctorProject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'time_doctor_project_id',
        'time_doctor_account_id',
        'time_doctor_company_id',
        'time_doctor_project_name',
        'time_doctor_project_description',
    ];

    public function account()
    {
        return $this->belongsTo(\App\TimeDoctor\TimeDoctorMember::class, 'time_doctor_account_id');
    }

    public function account_detail()
    {
        return $this->belongsTo(\App\TimeDoctor\TimeDoctorAccount::class, 'time_doctor_account_id');
    }
}
