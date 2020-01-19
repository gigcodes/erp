<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class DeveloperTaskDocument extends Model
{
    use Mediable;
    protected $fillable = ['subject', 'description', 'created_by', 'created_at', 'developer_task_id'];

    public function creator()
    {
        return $this->belongsTo("\App\User", "created_by", "id");
    }

}
