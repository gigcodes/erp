<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiDeveloperStatusHistoryLog extends Model
{
    protected $table = 'ui_developer_status_history_logs';

    protected $fillable = ['user_id', 'uichecks_id', 'old_status_id', 'status_id', 'created_at'];
}
