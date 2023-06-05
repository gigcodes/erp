<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MysqlCommandRunLog extends Model
{
    protected $fillable = [
        'user_id',
        'website_ids',
        'server_ip',
        'command',
        'response',
        'job_id',
        'status',
    ];
}
