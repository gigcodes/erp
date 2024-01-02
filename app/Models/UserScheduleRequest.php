<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserScheduleRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'request_date', 'request_status', 'updated_by'
    ];
}
