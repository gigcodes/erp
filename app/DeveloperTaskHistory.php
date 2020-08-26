<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeveloperTaskHistory extends Model
{
    protected $table = "developer_tasks_history";
    protected $fillable = [
        'user_id', 'developer_task_id', 'attribute', 'old_value', 'new_value','model','is_approved'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
