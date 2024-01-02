<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiCheckIssueHistoryLog extends Model
{
    protected $table = 'ui_check_issue_history_logs';

    protected $fillable = ['id', 'user_id', 'uichecks_id', 'old_issue', 'issue', 'created_at'];
}
