<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CronJobErroLog extends Model
{
    protected $table = 'cron_job_erro_logs';

    protected $fillable = ['id', 'signature', 'priority', 'error', 'error_count', 'status', 'module', 'subject', 'assigned_to', 'updated_at', 'created_at'];
}
