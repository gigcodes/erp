<?php

namespace App\Models;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'requested_user_id', 'remarks', 'request_status', 'requested_time', 'is_view', 'decline_remarks'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userrequest()
    {
        return $this->belongsTo(User::class, 'requested_user_id');
    }
}