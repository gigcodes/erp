<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskSubCategory extends Model
{
    //
    protected $table = 'task'
    public function task_category(){
        return $this->belongsTo(TaskCategories::class);
    }

    public function task_subject(){
        return $this->hasMany(TaskSubject::class,'task_subcategory_id','id');
    }
}
