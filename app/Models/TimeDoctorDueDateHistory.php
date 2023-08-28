<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimeDoctorDueDateHistory extends Model
{
    use HasFactory;

    public $table = 'time_doctor_account_due_date_histories';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
