<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoCommandRunLog extends Model
{
    protected $fillable = [
        'command_id',
        'user_id',
        'website_ids',
        'command_name',
        'server_ip',
        'command_type',
        'response',
        'job_id',
        'request'
    ];
}
