<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DatabaseExportCommandLog extends Model
{
    protected $table = 'database_export_command_logs';

    protected $fillable = [
        'user_id', 'command', 'response',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
