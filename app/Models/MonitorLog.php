<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitorLog extends Model
{
    use HasFactory;

    protected $table = 'monitor_log';
    protected $primaryKey = 'log_id';

    public $fillable = [
        'server_id',
        'type',
        'latency',
        'message',
        'datetime',
    ];

     /**
     * The monitorServers that belong to the monitorUser.
     */
    public function monitorServers()
    {
        return $this->belongsTo(MonitorServer::class);
    }

}
