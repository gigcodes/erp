<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskSubCategory extends Model
{
    //
    protected $table = 'task_sub_categories';

    protected $fillable = [
        'task_category_id', 'name',
    ];

    public function task_category()
    {
        return $this->belongsTo(TaskCategories::class);
    }

    public function task_subject()
    {
        return $this->hasMany(TaskSubject::class, 'task_subcategory_id', 'id');
    }
}
