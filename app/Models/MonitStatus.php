<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitStatus extends Model
{
    use HasFactory;

    protected $table = 'monit_status';

    protected $fillable = ['service_name', 'status', 'uptime', 'memory', 'url', 'username', 'password', 'xmlid', 'ip', 'monit_api_id'];

}