<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitorServersUptime extends Model
{
    use HasFactory;

    protected $table = 'monitor_servers_uptime';

    public $fillable = [
        'server_id',
        'date',
        'status',
        'latency',
    ];

    /**
     * Get the monitorServer that owns the monitorServersUptime.
     */
    public function monitorServer()
    {
        return $this->belongsTo(MonitorServer::class);
    }
}
