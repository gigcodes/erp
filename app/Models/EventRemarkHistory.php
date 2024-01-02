<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventRemarkHistory extends Model
{
    use HasFactory;

    public $table = 'event_remark_histories';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
