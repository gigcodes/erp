<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskSubject extends Model
{
    protected $table = 'task_subjects';

    protected $fillable = [
        'task_category_id', 'task_subcategory_id', 'name', 'description',
    ];

    public function category()
    {
        return $this->belongsTo(TaskSubCategory::class);
    }

    public function history()
    {
        return $this->hasMany(TaskHistories::class, 'task_subject_id', 'id');
    }
}
