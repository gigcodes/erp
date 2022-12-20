<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RedisQueueCommandExecutionLog extends Model
{
    protected $table = 'redis_queue_command_execution_log';

    protected $fillable = [
        'user_id', 'redis_queue_id', 'command', 'server_ip', 'response',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function queue()
    {
        return $this->belongsTo(\App\RedisQueue::class, 'redis_queue_id');
    }
}
