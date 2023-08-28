<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimeDoctorAccountRemarkHistory extends Model
{
    use HasFactory;

    public $table = 'time_doctor_accounts_remarks_history';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
