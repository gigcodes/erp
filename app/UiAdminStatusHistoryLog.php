<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiAdminStatusHistoryLog extends Model
{
    protected $table = 'ui_admin_status_history_logs';

    protected $fillable = ['user_id', 'uichecks_id', 'old_status_id', 'status_id', 'created_at'];
}
