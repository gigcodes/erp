<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZabbixTaskAssigneeHistory extends Model
{
    use HasFactory;

    public $fillable = [
        'zabbix_task_id',
        'old_assignee',
        'new_assignee',
        'user_id',
    ];

    public function zabbixTask()
    {
        return $this->belongsTo(ZabbixTask::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newAssignee()
    {
        return $this->belongsTo(User::class, 'new_assignee');
    }

    public function oldAssignee()
    {
        return $this->belongsTo(User::class, 'old_assignee');
    }
}
