<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class EventRemarkHistory extends Model
{
    use HasFactory;

    public $table = 'event_remark_histories';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
