<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatabaseBackupMonitoring extends Model
{
    use HasFactory;

    protected $table = 'database_backup_monitoring';


    public function dbStatusColour()
    {
        return $this->belongsTo(DatabaseBackupMonitoringStatus::class, 'db_status_id');
    }

}
