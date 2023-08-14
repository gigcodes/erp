<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatabaseBackupMonitoringStatus extends Model
{
    use HasFactory;

    protected $table = 'database_backup_monitoring_statuses';

    protected $fillable = ['name', 'color'];
}
