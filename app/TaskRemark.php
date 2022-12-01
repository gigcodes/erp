<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskRemark extends Model
{
    protected $fillable = ['task_id', 'task_type', 'updated_by', 'remark', 'create_at', 'updated_at'];

    public function users()
    {
        return $this->hasOne(\App\User::class, 'id', 'updated_by');
    }
}
