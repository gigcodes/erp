<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class TimeDoctorAccountRemarkHistory extends Model
{
    use HasFactory;

    public $table = 'time_doctor_accounts_remarks_history';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
