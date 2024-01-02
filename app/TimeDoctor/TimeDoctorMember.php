<?php

namespace App\TimeDoctor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeDoctorMember extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'time_doctor_user_id',
        'time_doctor_account_id',
        'email',
        'user_id',
        'pay_rate',
        'bill_rate',
        'currency',
    ];

    public static function linkUser($timeDoctorId, $userId)
    {
        self::where('time_doctor_user_id', $timeDoctorId)
            ->update([
                'user_id' => $userId,
            ]);
    }

    public function account()
    {
        return $this->belongsTo(\App\TimeDoctor\TimeDoctorMember::class, 'time_doctor_account_id');
    }

    public function account_detail()
    {
        return $this->belongsTo(\App\TimeDoctor\TimeDoctorAccount::class, 'time_doctor_account_id');
    }
}
