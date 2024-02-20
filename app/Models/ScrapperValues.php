<?php

namespace App\Models;

use App\DeveloperTask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScrapperValues extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'task_type', 'scrapper_values', 'added_by'];

    public function tasks()
    {
        return $this->belongsTo(DeveloperTask::class, 'task_id')->select('id', 'subject');
    }
}
