<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class TimeDoctorDueDateHistory extends Model
{
    use HasFactory;

    public $table = 'time_doctor_account_due_date_histories';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
