<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MagentoCronList extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'cron_name', 'server', 'server_ip', 'project_name', 'last_execution_time', 'last_message', 'cron_status', 'Frequency',
    ];
}
