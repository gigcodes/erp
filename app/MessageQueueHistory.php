<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageQueueHistory extends Model
{
    protected $table = 'message_queue_history';

    protected $fillable = [
        'number',
        'counter',
        'type',
        'user_id',
        'time',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }
}
